<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = Assignment::with([
            'volunteer.user',
            'workLocation',
            'task',
        ])->get();

        return $this->success(
            AssignmentResource::collection($assignments),
            'Assignments retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest $request)
    {
        $assignment = Assignment::create($request->validated());

        $assignment->refresh()->load([
            'volunteer.user',
            'workLocation',
            'task',
        ]);

        return $this->success(
            new AssignmentResource($assignment),
            'Assignment created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        $assignment->load(['volunteer.user', 'workLocation', 'task']);

        return $this->success(
            new AssignmentResource($assignment),
            'Assignment retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateAssignmentRequest $request,
        Assignment $assignment
    ) {
        $assignment->update($request->validated());

        $assignment->load([
            'volunteer.user',
            'workLocation',
            'task',
        ]);

        return $this->success(
            new AssignmentResource($assignment),
            'Assignment updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return $this->success(message: 'Assignment deleted successfully.');
    }

    public function myAssignments(Request $request)
    {
        $volunteer = $request->user()->volunteer;

        $assignments = $volunteer->assignments()
            ->with([
                'volunteer.user',
                'workLocation',
                'task',
            ])
            ->get();

        return $this->success(
            AssignmentResource::collection($assignments),
            'Your assignments were retrieved successfully.'
        );
    }
}
