<?php

use App\Http\Controllers\API\AttachmentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BoardController;
use App\Http\Controllers\API\ColumnController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskCommentController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Support\Facades\Route;

// Public authentication endpoints
Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

// Protected routes - all endpoints requiring authentication
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // User authentication management
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{project}/members', [ProjectController::class, 'addMember']);
    Route::delete('projects/{project}/members', [ProjectController::class, 'removeMember']);

    // Boards
    Route::apiResource('projects.boards', BoardController::class)->shallow();

    // Columns
    Route::apiResource('boards.columns', ColumnController::class)->shallow();
    Route::put('columns/reorder', [ColumnController::class, 'reorder']);

    // Tasks
    Route::apiResource('columns.tasks', TaskController::class)->shallow();
    Route::put('tasks/reorder', [TaskController::class, 'reorder']);
    Route::put('tasks/{task}/move', [TaskController::class, 'moveToColumn']);
    Route::post('tasks/{tasks}/assign', [TaskController::class, 'assignUser']);

    // Comments
    Route::apiResource('tasks.comments', TaskCommentController::class)->shallow();

    // Attachments
    Route::apiResource('tasks.attachments', AttachmentController::class)
        ->only(['index', 'store', 'destroy'])
        ->shallow();
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
