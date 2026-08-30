<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkLocationRequest;
use App\Http\Requests\UpdateWorkLocationRequest;
use App\Http\Resources\WorkLocationResource;
use App\Models\WorkLocation;

class WorkLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->success(
            WorkLocationResource::collection(WorkLocation::all()),
            'Work locations retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkLocationRequest $request)
    {
        $workLocation = WorkLocation::create(
            $request->validated()
        );

        return $this->success(
            new WorkLocationResource($workLocation),
            'Work location created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkLocation $workLocation)
    {
        return $this->success(
            new WorkLocationResource($workLocation),
            'Work location retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkLocationRequest $request, WorkLocation $workLocation)
    {
        $workLocation->update(
            $request->validated()
        );

        return $this->success(
            new WorkLocationResource($workLocation),
            'Work location updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkLocation $workLocation)
    {
        $workLocation->delete();

        return $this->success(message: 'Work location deleted successfully.');
    }
}
