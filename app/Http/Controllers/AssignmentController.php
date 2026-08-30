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

        return AssignmentResource::collection($assignments);
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

        return new AssignmentResource($assignment);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

        return new AssignmentResource($assignment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return response()->json([
            'message' => 'Assignment deleted successfully.',
        ]);
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

        return AssignmentResource::collection($assignments);
    }
}
