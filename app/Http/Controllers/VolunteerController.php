<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVolunteerRequest;
use App\Http\Requests\UpdateOwnProfileRequest;
use App\Http\Requests\UpdateVolunteerRequest;
use App\Http\Resources\VolunteerResource;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class VolunteerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $volunteers = Volunteer::with([
            'user',
            'assignments.volunteer.user',
            'assignments.workLocation',
            'assignments.task',
        ])->get();

        return $this->success(
            VolunteerResource::collection($volunteers),
            'Volunteers retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVolunteerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $volunteer = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'volunteer',
            ]);

            return Volunteer::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'],
                'national_id' => $validated['national_id'],
            ]);
        });

        $volunteer->load('user');

        return $this->success(
            new VolunteerResource($volunteer),
            'Volunteer created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Volunteer $volunteer)
    {
        $volunteer->load('user');

        return $this->success(
            new VolunteerResource($volunteer),
            'Volunteer retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateVolunteerRequest $request,
        Volunteer $volunteer
    ) {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $volunteer) {
            $userData = array_filter([
                'name' => $validated['name'] ?? null,
                'email' => $validated['email'] ?? null,
            ], fn ($value) => $value !== null);

            if ($userData) {
                $volunteer->user->update($userData);
            }

            $volunteerData = array_filter([
                'phone' => $validated['phone'] ?? null,
                'national_id' => $validated['national_id'] ?? null,
            ], fn ($value) => $value !== null);

            if ($volunteerData) {
                $volunteer->update($volunteerData);
            }
        });

        $volunteer->refresh()->load('user');

        return $this->success(
            new VolunteerResource($volunteer),
            'Volunteer updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Volunteer $volunteer)
    {
        if ($volunteer->assignments()->exists()) {
            return $this->error(
                'Volunteer cannot be deleted because they have assignments.',
                409
            );
        }

        DB::transaction(function () use ($volunteer) {
            $user = $volunteer->user;

            $volunteer->delete();

            if ($user) {
                $user->delete();
            }
        });

        return $this->success(message: 'Volunteer deleted successfully.');
    }

    public function me(Request $request)
    {
        $volunteer = $request->user()->volunteer;

        if (! $volunteer) {
            return $this->error('Volunteer profile not found.', 404);
        }

        $volunteer->load('user');

        return $this->success(
            new VolunteerResource($volunteer),
            'Profile retrieved successfully.'
        );
    }

    public function updateMe(UpdateOwnProfileRequest $request)
    {
        $volunteer = $request->user()->volunteer;

        if (! $volunteer) {
            return $this->error('Volunteer profile not found.', 404);
        }

        Gate::authorize('update', $volunteer);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $volunteer) {
            $userData = array_filter([
                'name' => $validated['name'] ?? null,
                'email' => $validated['email'] ?? null,
            ], fn ($value) => $value !== null);

            if ($userData) {
                $volunteer->user->update($userData);
            }

            $volunteerData = array_filter([
                'phone' => $validated['phone'] ?? null,
            ], fn ($value) => $value !== null);

            if ($volunteerData) {
                $volunteer->update($volunteerData);
            }
        });

        $volunteer->refresh()->load('user');

        return $this->success(
            new VolunteerResource($volunteer),
            'Profile updated successfully.'
        );
    }
}
