<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            'App\Interfaces\RoleRepositoryInterface',
            'App\Repositories\RoleRepository'
        );
        $this->app->bind(
            'App\Interfaces\CouncilRepositoryInterface',
            'App\Repositories\CouncilRepository'
        );
        $this->app->bind(
            'App\Interfaces\UserRepositoryInterface',
            'App\Repositories\UserRepository'
        );
        $this->app->bind(
            'App\Interfaces\TaskRepositoryInterface',
            'App\Repositories\TaskRepository'
        );
        $this->app->bind(
            'App\Interfaces\TaskSubmissionRepositoryInterface',
            'App\Repositories\TaskSubmissionRepository'
        );
        $this->app->bind(
            'App\Interfaces\AttendanceRepositoryInterface',
            'App\Repositories\AttendanceRepository'
        );
        $this->app->bind(
            'App\Interfaces\CouncilSessionRepositoryInterface',
            'App\Repositories\CouncilSessionRepository'
        );
        $this->app->bind(
            'App\Interfaces\TeamRepositoryInterface',
            'App\Repositories\TeamRepository'
        );
        $this->app->bind(
            'App\Interfaces\TeamMemberRepositoryInterface',
            'App\Repositories\TeamMemberRepository'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
