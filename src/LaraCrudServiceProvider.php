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

        // Form Input Maker
        Blade::directive('input', function ($expression) {
            return "<?php echo FormBuilder::input(".$expression."); ?>";    
        });
        
        // Form Form Maker
        Blade::directive('form', function ($expression) {
            return "<?php echo FormBuilder::form(".$expression."); ?>";
        });
        
        // Form Maker - Display Values
        Blade::directive('display', function ($expression) {
            return "<?php echo FormBuilder::display(".$expression."); ?>";
        });
        
        // Form Maker - DisplayAll Values
        Blade::directive('displayAll', function ($expression) {
            return "<?php echo FormBuilder::displayAll(".$expression."); ?>";
        });
        
        // Form Maker - Check Whether User has Module Access
        Blade::directive('access', function ($expression) {
            return "<?php if(FormBuilder::access(".$expression.")) { ?>";
        });
        Blade::directive('endaccess', function ($expression) {
            return "<?php } ?>";
        });
        
        // Form Maker - Check Whether User has Module Access
        Blade::directive('pageAccess', function ($expression) {
            return "<?php if(FormBuilder::pageAccess(".$expression.")) { ?>";
        });
        Blade::directive('endpageAccess', function ($expression) {
            return "<?php } ?>";
        });

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
