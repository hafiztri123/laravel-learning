<?php

use App\Http\Controllers\API\BoardController;
use App\Http\Controllers\API\ColumnController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskCommentController;
use App\Http\Controllers\API\TaskController;

Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{project}/members', [ProjectController::class, 'addMember']);
    Route::delete('projects/{project}/members', [ProjectController::class, 'removeMember']);

    Route::apiResource('projects.boards', BoardController::class)->shallow();

    Route::apiResource('boards.columns', ColumnController::class)->shallow();
    Route::put('columns/reorder', [ColumnController::class, 'reorder']);

    Route::apiResource('columns.tasks', TaskController::class)->shallow();
    Route::put('tasks/reorder', [TaskController::class, 'reorder']);
    Route::put('tasks/{task}/move', [TaskController::class, 'moveToColumn']);
    Route::post('tasks/{tasks}/assign', [TaskController::class, 'assignUser']);

    Route::apiResource('tasks.comments', TaskCommentController::class)->shallow();




});

