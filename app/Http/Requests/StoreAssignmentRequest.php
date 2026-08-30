<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'volunteer_id' => 'required|exists:volunteers,id',
            'work_location_id' => 'required|exists:work_locations,id',
            'task_id' => [
                'required',
                'exists:tasks,id',
                Rule::unique('assignments')->where(function ($query) {
                    return $query
                        ->where('volunteer_id', $this->volunteer_id)
                        ->where('work_location_id', $this->work_location_id);
                }),
            ],
            'assignment_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'task_id.unique' => 'This volunteer already has the same task at this work location.',
        ];
    }
}
