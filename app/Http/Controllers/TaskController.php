<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->success(
            TaskResource::collection(Task::all()),
            'Tasks retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create(
            $request->validated()
        );

        return $this->success(
            new TaskResource($task),
            'Task created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return $this->success(
            new TaskResource($task),
            'Task retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update(
            $request->validated()
        );

        return $this->success(
            new TaskResource($task),
            'Task updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return $this->success(message: 'Task deleted successfully.');
    }
}
