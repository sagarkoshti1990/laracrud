<?php

namespace Sagartakle\Laracrud;

use Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\AliasLoader;
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
        $this->app->make('Sagartakle\Laracrud\LaraCrudServiceProvider');
        /*
        |--------------------------------------------------------------------------
        | Register the Commands
        |--------------------------------------------------------------------------
        */
        
        $this->commands([
            Sagartakle\Laracrud\Console\Commands\ConfigActivityLogsCommand::class,
            Sagartakle\Laracrud\Console\Commands\CrudCommand::class,
            Sagartakle\Laracrud\Console\Commands\CrudControllerCommand::class,
            Sagartakle\Laracrud\Console\Commands\CrudMigrateCommand::class,
            Sagartakle\Laracrud\Console\Commands\CrudViewCreateCommand::class,
            Sagartakle\Laracrud\Console\Commands\CrudViewEditCommand::class,
            Sagartakle\Laracrud\Console\Commands\CrudViewIndexCommand::class,
            Sagartakle\Laracrud\Console\Commands\CrudViewShowCommand::class,
            Sagartakle\Laracrud\Console\Commands\Inspire::class,
            Sagartakle\Laracrud\Console\Commands\CrudModelCommand::class
        ]);
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
        // $this->publishes([
        //     __DIR__.'/views' => base_path('View'),
        // ]);


    }
}
