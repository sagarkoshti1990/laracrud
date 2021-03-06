<?php

namespace Sagartakle\Laracrud;

use Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Sagartakle\Laracrud\Helpers\FormBuilder;
use Sagartakle\Laracrud\Helpers\CustomHelper;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\Field;
use Sagartakle\Laracrud\Models\FieldType;
use Sagartakle\Laracrud\Helpers\ObjectHelper;
use Sagartakle\Laracrud\Models\Activity;

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
            \Sagartakle\Laracrud\Console\Commands\Stlc::class,
            \Sagartakle\Laracrud\Console\Commands\Controller::class,
            \Sagartakle\Laracrud\Console\Commands\Migrate::class,
            \Sagartakle\Laracrud\Console\Commands\ViewCreate::class,
            \Sagartakle\Laracrud\Console\Commands\ViewEdit::class,
            \Sagartakle\Laracrud\Console\Commands\ViewIndex::class,
            \Sagartakle\Laracrud\Console\Commands\ViewShow::class,
            \Sagartakle\Laracrud\Console\Commands\Model::class,
            \Sagartakle\Laracrud\Console\Commands\Menu::class
        ]);

        $this->app->booting(function() {
            $loader = AliasLoader::getInstance();
            $loader->alias('FormBuilder', config('stlc.form_builder',FormBuilder::class));
            $loader->alias('CustomHelper', config('stlc.custom_helper',CustomHelper::class));
            $loader->alias('Module', config('stlc.module_model',Module::class));
            $loader->alias('Field', config('stlc.field_model',Field::class));
            $loader->alias('FieldType', config('stlc.field_type_model',FieldType::class));
            $loader->alias('ObjectHelper', config('stlc.object_helper',ObjectHelper::class));
            $loader->alias('Carbon', \Carbon\Carbon::class);
            $loader->alias('Activity', config('stlc.activity_model',Activity::class));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if(file_exists(base_path('routes'.DIRECTORY_SEPARATOR.'stlc.php'))) {
            $this->loadRoutesFrom(base_path('routes'.DIRECTORY_SEPARATOR.'stlc.php'));
        } else if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'Routes'.DIRECTORY_SEPARATOR.'stlc.php')) {
            $this->loadRoutesFrom(__DIR__.DIRECTORY_SEPARATOR.'Routes'.DIRECTORY_SEPARATOR.'stlc.php');
        }
        $this->loadViewsFrom(__DIR__.'/View', 'stlc');
        // $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'stlc.php' => base_path('config'.DIRECTORY_SEPARATOR.'stlc.php'),
            // Routes
            __DIR__.DIRECTORY_SEPARATOR.'Routes'.DIRECTORY_SEPARATOR.'stlc.php' => base_path('routes'.DIRECTORY_SEPARATOR.'stlc.php'),
            // migration
            __DIR__.DIRECTORY_SEPARATOR.'Migrations' => base_path('database'.DIRECTORY_SEPARATOR.'migrations'),
            // Model
            __DIR__.DIRECTORY_SEPARATOR.'Console'.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'user.stub' => base_path('app'.DIRECTORY_SEPARATOR.'User.php'),
            // 
            __DIR__.DIRECTORY_SEPARATOR.'Lang'.DIRECTORY_SEPARATOR.'stlc.php' => resource_path('lang'.DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR.'stlc.php'),
            __DIR__.DIRECTORY_SEPARATOR.'package.json' => base_path('package.json'),
            __DIR__.DIRECTORY_SEPARATOR.'public' => base_path('public'),
        ],'public');

        $this->publishes([
            // stlc auth
            __DIR__.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Auth' => base_path('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'Auth'),
            // stlc auth view
            __DIR__.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.'auth' => base_path('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'auth'),
            // stlc layouts
            __DIR__.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.'layouts' => base_path('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'layouts'),
        ], 'auth');

        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'View' => base_path('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'themes'),
        ], 'view');

        Blade::directive('pushonce', function ($expression) {
            $var = '$__env->{"__pushonce_" . md5(__FILE__ . ":" . __LINE__)}';
            return "<?php if(!isset({$var})): {$var} = true; \$__env->startPush({$expression}); ?>";
        });
        
        Blade::directive('endpushonce', function ($expression) {
            return '<?php $__env->stopPush(); endif; ?>';
        });

        // Form Input Maker
        Blade::directive('input', function ($expression) {
            return "<?php echo \FormBuilder::input(".$expression."); ?>";    
        });
        
        // Form Form Maker
        Blade::directive('form', function ($expression) {
            return "<?php echo \FormBuilder::form(".$expression."); ?>";
        });
        
        // Form Maker - Display Values
        Blade::directive('display', function ($expression) {
            return "<?php echo \FormBuilder::display(".$expression."); ?>";
        });
        
        // Form Maker - DisplayAll Values
        Blade::directive('displayAll', function ($expression) {
            return "<?php echo \FormBuilder::displayAll(".$expression."); ?>";
        });
        
        // Form Maker - Check Whether User has Module Access
        Blade::directive('access', function ($expression) {
            return "<?php if(\FormBuilder::access(".$expression.")) { ?>";
        });
        Blade::directive('endaccess', function ($expression) {
            return "<?php } ?>";
        });
        
        // Form Maker - Check Whether User has Module Access
        Blade::directive('pageAccess', function ($expression) {
            return "<?php if(\FormBuilder::pageAccess(".$expression.")) { ?>";
        });
        Blade::directive('endpageAccess', function ($expression) {
            return "<?php } ?>";
        });
        
        // Form Maker - Check Whether User has Module Access
        Blade::directive('hasRoles', function ($expression) {
            return "<?php if(\Module::user()->hasRoles(".$expression.")) { ?>";
        });
        Blade::directive('endhasRoles', function ($expression) {
            return "<?php } ?>";
        });

        Blade::directive('ajprint', function ($expression) {
            return "<?php \CustomHelper::ajprint(".$expression.") ?>";
        });
        
        Blade::directive('attributes', function ($expression) {
            return "<?php echo \CustomHelper::attributes(".$expression."); ?>";
        });
    }
}
