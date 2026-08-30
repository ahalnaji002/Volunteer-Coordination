<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'volunteer' => [
                'id' => $this->volunteer->id,
                'name' => $this->volunteer->user->name,
            ],

            'work_location' => [
                'id' => $this->workLocation->id,
                'name' => $this->workLocation->name,
            ],

            'task' => [
                'id' => $this->task->id,
                'name' => $this->task->name,
            ],

            'assignment_date' => $this->assignment_date,
            'status' => $this->status,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
