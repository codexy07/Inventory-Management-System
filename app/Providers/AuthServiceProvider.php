<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Admin gate - full access
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        // Resource creation gate
        Gate::define('create', function ($user) {
            return $user->role === 'admin';
        });

        // Resource editing gate
        Gate::define('edit', function ($user) {
            return $user->role === 'admin';
        });

        // Resource deletion gate
        Gate::define('delete', function ($user) {
            return $user->role === 'admin';
        });

        // Report export gate
        Gate::define('export-reports', function ($user) {
            return $user->role === 'admin';
        });
    }
}
