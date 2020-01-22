<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LaraCrudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('sagar\laracrud\TodolistController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/admin.php');
        $this->loadRoutesFrom(__DIR__.'/base.php');
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->publishes([
            __DIR__.'/views' => base_path('View'),
        ]);
    }
}
