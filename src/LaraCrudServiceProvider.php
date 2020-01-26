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
        $this->loadRoutesFrom(base_path('routes'.DIRECTORY_SEPARATOR.'stlc.php'));
        // $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'stlc.php' => base_path('config'.DIRECTORY_SEPARATOR.'stlc.php'),

            // Routes
            __DIR__.DIRECTORY_SEPARATOR.'Routes'.DIRECTORY_SEPARATOR.'admin.php' => base_path('routes'.DIRECTORY_SEPARATOR.'stlc.php'),

            // stlc auth
            __DIR__.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'StlcAuth' => base_path('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'StlcAuth'),
            
            // stlc auth view
            __DIR__.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.'auth' => base_path('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'stlcauth'),

            // migration
            __DIR__.DIRECTORY_SEPARATOR.'Migrations' => base_path('database'.DIRECTORY_SEPARATOR.'migrations'),

            // 
            __DIR__.DIRECTORY_SEPARATOR.'package.json' => base_path('package.json'),
        ]);
    }
}
