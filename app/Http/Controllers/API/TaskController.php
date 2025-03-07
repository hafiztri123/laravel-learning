<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Column $column)
    {
        $this->authorize('view', $column);

        $tasks = $column->tasks;

        return response()->json($tasks);



    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Column $column)
    {
        $this->authorize('update', $column);

        $request->validate([
            "title" => 'required|string|max:255',
            "description" => 'sometimes|required|string',
            "order" => 'required|integer',
            "assignee_id" => 'sometimes|required|exists:users,id',
            "due_date" => 'required|date|after_or_equal:today',
        ]);

        $user = Auth::user();

        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->order = $request->order;
        $task->due_date = $request->due_date;
        $task->assignee_id = $request->assignee_id;
        $task->created_by = $user->id;

        $task->save();

        return response()->json($task, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            "title" => 'sometimes|required|string|max:255',
            "description" => 'sometimes|required|string',
            "order" => 'sometimes|required|integer',
            "due_date" => 'sometimes|required|date|after_or_equal:today',
        ]);

        $task->update($request->only(['title', 'description', 'order', 'assignee_id', 'due_date']));

        return response()->json($task);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return reponse()->json(null, 204);
    }

    public function moveToColumn(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            "column_id" => 'sometimes|required|integer'
        ]);

        $task->update($request->only(['column_id']));

        return response()->json($task);

    }

    public function assignUser(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            "assignee_id" => 'required|exists:users,id'
        ]);

        $task->update($request->only(['assignee_id']));

        return response()->json($task);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.order' => 'required|integer|min:0'
        ]);

        $tasksData = $request->tasks;

        DB::beginTransaction();

        try {
            foreach($tasksData as $taskData){
                $task = Task::findOrFail($taskData['id']);
                $this->authorize('update', $task);
                $task->update(['order' => $taskData['order']]);
             }

             DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Error reordering tasks'], 500);
        }


    }
}
