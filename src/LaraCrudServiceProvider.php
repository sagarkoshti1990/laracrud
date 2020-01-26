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
        // $this->app->make('Sagartakle\Laracrud\LaraCrudServiceProvider');
        /*
        |--------------------------------------------------------------------------
        | Register the Commands
        |--------------------------------------------------------------------------
        */
        
        $this->commands([
            \Sagartakle\Laracrud\Console\Commands\ConfigActivityLogsCommand::class,
            \Sagartakle\Laracrud\Console\Commands\StlcCommand::class,
            \Sagartakle\Laracrud\Console\Commands\ControllerCommand::class,
            \Sagartakle\Laracrud\Console\Commands\MigrateCommand::class,
            \Sagartakle\Laracrud\Console\Commands\ViewCreateCommand::class,
            \Sagartakle\Laracrud\Console\Commands\ViewEditCommand::class,
            \Sagartakle\Laracrud\Console\Commands\ViewIndexCommand::class,
            \Sagartakle\Laracrud\Console\Commands\ViewShowCommand::class,
            \Sagartakle\Laracrud\Console\Commands\ModelCommand::class
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/admin.php');
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->publishes([
            __DIR__.'/Config/stlc.php' => base_path('config/stlc.php'),
        ]);
    }
}
