<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Task $task)
    {
        $this->authorize('view', $task);

        $comments = $task->comments;

        return response()->json($comments);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $request->validate([
            'content' => 'required|string',
        ]);

        $user = Auth::user();

        $comment = new TaskComment();
        $comment->content = $request->content;
        $comment->task_id  = $task->id;
        $comment->user_id = $user->id;
        $comment->save();

        return response()->json($comment, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(TaskComment $taskComment)
    {
        $this->authorize('view', $taskComment);

        return response()->json($taskComment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskComment $taskComment)
    {
        $this->authorize('update', $taskComment);

        $request->validate([
            'content' => 'sometimes|required|string'
        ]);

        $taskComment->update(['content' => $request->content]);

        return response()->json($taskComment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskComment $taskComment)
    {
        $this->authorize('destory', $taskComment);

        $taskComment->delete();

        return response()->json(null, 204);

    }
}
