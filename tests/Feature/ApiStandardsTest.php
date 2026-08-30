<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Task;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\WorkLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiStandardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_api_request_uses_error_envelope(): void
    {
        $this->getJson('/api/tasks')
            ->assertUnauthorized()
            ->assertExactJson([
                'status' => 'error',
                'message' => 'Unauthenticated.',
                'data' => null,
            ]);
    }

    public function test_volunteer_can_view_tasks_with_success_envelope(): void
    {
        Sanctum::actingAs($this->volunteerUser());
        Task::create(['name' => 'First aid']);

        $this->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Tasks retrieved successfully.')
            ->assertJsonPath('data.0.name', 'First aid');
    }

    public function test_volunteer_cannot_use_admin_write_endpoint(): void
    {
        Sanctum::actingAs($this->volunteerUser());

        $this->postJson('/api/tasks', ['name' => 'Distribution'])
            ->assertForbidden()
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('data', null);
    }

    public function test_admin_cannot_use_volunteer_self_service_endpoint(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->getJson('/api/me')
            ->assertForbidden()
            ->assertJsonPath('status', 'error');
    }

    public function test_self_service_ignores_another_volunteer_id(): void
    {
        $user = $this->volunteerUser();
        $otherUser = User::factory()->create(['role' => 'volunteer']);
        $other = Volunteer::create([
            'user_id' => $otherUser->id,
            'phone' => '0591111111',
            'national_id' => '900000002',
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/me?volunteer_id='.$other->id)
            ->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonMissing(['email' => $otherUser->email]);
    }

    public function test_user_resource_never_exposes_sensitive_fields(): void
    {
        $user = $this->volunteerUser();
        Sanctum::actingAs($user);

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.remember_token');
    }

    public function test_validation_error_uses_standard_envelope(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->postJson('/api/tasks', [])
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonPath('data', null)
            ->assertJsonValidationErrors('name');
    }

    public function test_missing_model_uses_resource_not_found_envelope(): void
    {
        Sanctum::actingAs($this->volunteerUser());

        $this->getJson('/api/tasks/999999')
            ->assertNotFound()
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_missing_endpoint_uses_endpoint_not_found_envelope(): void
    {
        $this->getJson('/api/not-a-real-endpoint')
            ->assertNotFound()
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Endpoint not found.');
    }

    public function test_wrong_method_uses_method_not_allowed_envelope(): void
    {
        $this->putJson('/api/login')
            ->assertMethodNotAllowed()
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Method not allowed.');
    }

    public function test_admin_can_use_admin_write_endpoint(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->postJson('/api/tasks', [
            'name' => 'Distribution',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Task created successfully.')
            ->assertJsonPath('data.name', 'Distribution');
    }

    public function test_login_returns_standard_success_envelope(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
            'role' => 'volunteer',
        ]);

        Volunteer::create([
            'user_id' => $user->id,
            'phone' => '0590000000',
            'national_id' => '900000003',
        ]);

        $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Login successful.')
            ->assertJsonPath('data.user.email', 'login@example.com')
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'token',
                ],
            ]);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create([
            'role' => 'volunteer',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Logout successful.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_admin_can_create_an_assignment_successfully(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $volunteer = $this->volunteerUser();
        $workLocation = WorkLocation::create(['name' => 'Al-Shifa Hospital']);
        $task = Task::create(['name' => 'First aid']);

        $this->postJson('/api/assignments', [
            'volunteer_id' => $volunteer->volunteer->id,
            'work_location_id' => $workLocation->id,
            'task_id' => $task->id,
            'assignment_date' => '2026-08-31',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.volunteer.id', $volunteer->volunteer->id)
            ->assertJsonPath('data.work_location.id', $workLocation->id)
            ->assertJsonPath('data.task.id', $task->id);

        $this->assertDatabaseHas('assignments', [
            'volunteer_id' => $volunteer->volunteer->id,
            'work_location_id' => $workLocation->id,
            'task_id' => $task->id,
        ]);
    }

    public function test_duplicate_assignment_returns_validation_error(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $volunteer = $this->volunteerUser()->volunteer;
        $workLocation = WorkLocation::create(['name' => 'Distribution Center']);
        $task = Task::create(['name' => 'Distribution']);

        Assignment::create([
            'volunteer_id' => $volunteer->id,
            'work_location_id' => $workLocation->id,
            'task_id' => $task->id,
            'assignment_date' => '2026-08-31',
        ]);

        $this->postJson('/api/assignments', [
            'volunteer_id' => $volunteer->id,
            'work_location_id' => $workLocation->id,
            'task_id' => $task->id,
            'assignment_date' => '2026-09-01',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonValidationErrors('task_id');
    }

    public function test_my_assignments_returns_only_the_authenticated_volunteers_assignments(): void
    {
        $user = $this->volunteerUser();
        $otherUser = User::factory()->create(['role' => 'volunteer']);
        $otherVolunteer = Volunteer::create([
            'user_id' => $otherUser->id,
            'phone' => '0591111111',
            'national_id' => '900000004',
        ]);
        $workLocation = WorkLocation::create(['name' => 'Community Center']);
        $task = Task::create(['name' => 'Monitoring']);

        $ownAssignment = Assignment::create([
            'volunteer_id' => $user->volunteer->id,
            'work_location_id' => $workLocation->id,
            'task_id' => $task->id,
            'assignment_date' => '2026-08-31',
        ]);
        Assignment::create([
            'volunteer_id' => $otherVolunteer->id,
            'work_location_id' => $workLocation->id,
            'task_id' => $task->id,
            'assignment_date' => '2026-09-01',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/my-assignments')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownAssignment->id)
            ->assertJsonMissing(['volunteer' => ['id' => $otherVolunteer->id]]);
    }

    private function volunteerUser(): User
    {
        $user = User::factory()->create(['role' => 'volunteer']);

        Volunteer::create([
            'user_id' => $user->id,
            'phone' => '0590000000',
            'national_id' => '900000001',
        ]);

        return $user;
    }
}
