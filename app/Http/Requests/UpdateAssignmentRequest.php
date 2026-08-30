<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'work_location_id' => 'sometimes|exists:work_locations,id',
            'task_id' => 'sometimes|exists:tasks,id',
            'assignment_date' => 'sometimes|date',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $assignment = $this->route('assignment');

                $workLocationId = $this->input(
                    'work_location_id',
                    $assignment->work_location_id
                );

                $taskId = $this->input(
                    'task_id',
                    $assignment->task_id
                );

                $duplicateExists = Assignment::query()
                    ->where('volunteer_id', $assignment->volunteer_id)
                    ->where('work_location_id', $workLocationId)
                    ->where('task_id', $taskId)
                    ->where('id', '!=', $assignment->id)
                    ->exists();

                if ($duplicateExists) {
                    $validator->errors()->add(
                        'assignment',
                        'This volunteer already has the same task at this work location.'
                    );
                }
            },
        ];
    }
}
