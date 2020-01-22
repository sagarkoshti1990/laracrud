<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use App\Models\Module;

class CrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lara:crud {name?} {--option=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'lara:crud {name?} {--option=["views","migrate","model","controller","request","viewIndex","viewCreate","viewEdit","viewShow"]} ';

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
                        Artisan::call('lara:migrate', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "model")) {
                        // Create the CRUD Model and show output
                        Artisan::call('lara:model', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "controller")) {
                        // Create the CRUD Controller and show output
                        Artisan::call('lara:controller', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "viewIndex")) {
                        // Create the CRUD Request and show output
                        Artisan::call('lara:viewIndex', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "viewCreate")) {
                        // Create the CRUD Request and show output
                        Artisan::call('lara:viewCreate', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "viewEdit")) {
                        // Create the CRUD Request and show output
                        Artisan::call('lara:viewEdit', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "viewShow")) {
                        // Create the CRUD Request and show output
                        Artisan::call('lara:viewShow', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "views")) {
                        // Create the CRUD Request and show output
                        Artisan::call('lara:viewIndex', ['name' => $value->name]);
                        // Create the CRUD Request and show output
                        echo Artisan::output();
                        Artisan::call('lara:viewCreate', ['name' => $value->name]);
                        echo Artisan::output();
                        // Create the CRUD Request and show output
                        Artisan::call('lara:viewEdit', ['name' => $value->name]);
                        echo Artisan::output();
                        // Create the CRUD Request and show output
                        Artisan::call('lara:viewShow', ['name' => $value->name]);
                        echo Artisan::output();
                    } else if(($this->option('option') == "all")) {
                        // Create the CRUD Request and show output
                        Artisan::call('lara:migrate', ['name' => $value->name]);
                        echo Artisan::output();

                        // // Create the CRUD Model and show output
                        Artisan::call('lara:model', ['name' => $value->name]);
                        echo Artisan::output();

                        // Create the CRUD Controller and show output
                        Artisan::call('lara:controller', ['name' => $value->name]);
                        echo Artisan::output();

                        // Create the CRUD Request and show output
                        Artisan::call('lara:viewIndex', ['name' => $value->name]);
                        echo Artisan::output();

                        // // Create the CRUD Request and show output
                        Artisan::call('lara:viewCreate', ['name' => $value->name]);
                        echo Artisan::output();

                        // // Create the CRUD Request and show output
                        Artisan::call('lara:viewEdit', ['name' => $value->name]);
                        echo Artisan::output();

                        // // Create the CRUD Request and show output
                        Artisan::call('lara:viewShow', ['name' => $value->name]);
                        echo Artisan::output();
                    }
                }
            }
        } else if($this->argument('name')) {
            $name = ucfirst(\Str::plural($this->argument('name')));
            // Create the CRUD Request and show output
            Artisan::call('lara:migrate', ['name' => $name]);
            echo Artisan::output();
            
            // Create the CRUD Request and show output
            Artisan::call('migrate');
            echo Artisan::output();

            // // Create the CRUD Model and show output
            Artisan::call('lara:model', ['name' => $name]);
            echo Artisan::output();

            // Create the CRUD Controller and show output
            Artisan::call('lara:controller', ['name' => $name]);
            echo Artisan::output();

            if($this->option('option') && $this->option('option') == "with-views") {
                // Create the CRUD Request and show output
                Artisan::call('lara:viewIndex', ['name' => $name]);
                echo Artisan::output();

                // // Create the CRUD Request and show output
                Artisan::call('lara:viewCreate', ['name' => $name]);
                echo Artisan::output();

                // // Create the CRUD Request and show output
                Artisan::call('lara:viewEdit', ['name' => $name]);
                echo Artisan::output();

                // // Create the CRUD Request and show output
                Artisan::call('lara:viewShow', ['name' => $name]);
                echo Artisan::output();
            }
        }
        // Create the CRUD log_config
        // Artisan::call('lara:log_config');
        // echo Artisan::output();
    }
}
