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
        return WorkLocationResource::collection(
            WorkLocation::all()
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

        return (new WorkLocationResource($workLocation))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkLocation $workLocation)
    {
        return new WorkLocationResource($workLocation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkLocationRequest $request, WorkLocation $workLocation)
    {
        $workLocation->update(
            $request->validated()
        );

        return new WorkLocationResource($workLocation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkLocation $workLocation)
    {
        $workLocation->delete();

        return response()->noContent();
    }
}