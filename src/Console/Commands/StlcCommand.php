<?php

namespace Sagartakle\Laracrud\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Sagartakle\Laracrud\Models\Module;

class StlcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stlc:crud {name?} {--option=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'stlc:crud {name?} {--option=["views","migrate","model","controller","request","viewIndex","viewCreate","viewEdit","viewShow"]} ';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('option') && $this->option('option') != "with-views") {
            $this->info($this->option('option'));
            foreach (Module::all() as $key => $value) {
                if(!in_array($value->name, ['Users','Uploads','Permissions','Roles','Employees','Tests'])) {
                    $this->info($value->name);
                    if(($this->option('option') == "migrate")) {
                        Artisan::call('stlc:migrate', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "model")) {
                        // Create the CRUD Model and show output
                        Artisan::call('stlc:model', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "controller")) {
                        // Create the CRUD Controller and show output
                        Artisan::call('stlc:controller', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "viewIndex")) {
                        // Create the CRUD Request and show output
                        Artisan::call('stlc:viewIndex', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "viewCreate")) {
                        // Create the CRUD Request and show output
                        Artisan::call('stlc:viewCreate', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "viewEdit")) {
                        // Create the CRUD Request and show output
                        Artisan::call('stlc:viewEdit', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "viewShow")) {
                        // Create the CRUD Request and show output
                        Artisan::call('stlc:viewShow', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "views")) {
                        // Create the CRUD Request and show output
                        Artisan::call('stlc:viewIndex', ['name' => $value->name]);
                        // Create the CRUD Request and show output
                        echo Artisan::output();
                        Artisan::call('stlc:viewCreate', ['name' => $value->name]);
                        echo Artisan::output();
                        // Create the CRUD Request and show output
                        Artisan::call('stlc:viewEdit', ['name' => $value->name]);
                        echo Artisan::output();
                        // Create the CRUD Request and show output
                        Artisan::call('stlc:viewShow', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "all")) {
                        // Create the CRUD Request and show output
                        Artisan::call('stlc:migrate', ['name' => $value->name]);
                        echo Artisan::output();

                        // // Create the CRUD Model and show output
                        Artisan::call('stlc:model', ['name' => $value->name]);
                        echo Artisan::output();

                        // Create the CRUD Controller and show output
                        Artisan::call('stlc:controller', ['name' => $value->name]);
                        echo Artisan::output();

                        // Create the CRUD Request and show output
                        Artisan::call('stlc:viewIndex', ['name' => $value->name]);
                        echo Artisan::output();

                        // // Create the CRUD Request and show output
                        Artisan::call('stlc:viewCreate', ['name' => $value->name]);
                        echo Artisan::output();

                        // // Create the CRUD Request and show output
                        Artisan::call('stlc:viewEdit', ['name' => $value->name]);
                        echo Artisan::output();

                        // // Create the CRUD Request and show output
                        Artisan::call('stlc:viewShow', ['name' => $value->name]);
                        echo Artisan::output();
                    }
                }
            }
        } else if($this->argument('name')) {
            $name = ucfirst(\Str::plural($this->argument('name')));
            // Create the CRUD Request and show output
            Artisan::call('stlc:migrate', ['name' => $name]);
            echo Artisan::output();
            
            // Create the CRUD Request and show output
            Artisan::call('migrate');
            echo Artisan::output();

            // // Create the CRUD Model and show output
            Artisan::call('stlc:model', ['name' => $name]);
            echo Artisan::output();

            // Create the CRUD Controller and show output
            Artisan::call('stlc:controller', ['name' => $name]);
            echo Artisan::output();

            if($this->option('option') && $this->option('option') == "with-views") {
                // Create the CRUD Request and show output
                Artisan::call('stlc:viewIndex', ['name' => $name]);
                echo Artisan::output();

                // // Create the CRUD Request and show output
                Artisan::call('stlc:viewCreate', ['name' => $name]);
                echo Artisan::output();

                // // Create the CRUD Request and show output
                Artisan::call('stlc:viewEdit', ['name' => $name]);
                echo Artisan::output();

                // // Create the CRUD Request and show output
                Artisan::call('stlc:viewShow', ['name' => $name]);
                echo Artisan::output();
            }
        }
        // Create the CRUD log_config
        // Artisan::call('stlc:log_config');
        // echo Artisan::output();
    }
}
