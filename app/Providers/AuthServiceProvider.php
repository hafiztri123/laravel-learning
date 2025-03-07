<?php

namespace App\Providers;

use App\Models\Attachment;
use App\Models\Board;
use App\Models\Column;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Policies\AttachmentPolicy;
use App\Policies\BoardPolicy;
use App\Policies\ColumnPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskCommentPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Board::class => BoardPolicy::class,
        Task::class => TaskPolicy::class,
        Column::class => ColumnPolicy::class,
        Attachment::class => AttachmentPolicy::class,
        TaskComment::class => TaskCommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

    }
}
