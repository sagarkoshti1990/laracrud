<?php

namespace Sagartakle\laracrud\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Sagartakle\laracrud\Helpers\Inflect;
use Sagartakle\laracrud\Models\Module;

class ConfigActivityLogsCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lara:log_config';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lara:log_config{name=activity_log}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modules config for default activity logs';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Config';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/activity_config.stub';
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        // $name = $this->getNameInput();

        $name = "activity_log";

        $path = $this->getPath($name);

        if ($this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }
        
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Replace the table name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceNameStrings(&$stub)
    {
        $name = "activity_log";
        $moduls = Module::all();
        $out = "";
        if(isset($moduls) && $moduls->count()) {
            foreach ($moduls as $key => $modul) {
                $out .= "\t'".$modul->name."' => [\n";
                $out .= "\t\t'CREATED' => ['action' => 'Created', 'description' => '".$modul->model." It has been created'],\n";
                $out .= "\t\t'UPDATED' => ['action' => 'Updated', 'description' => '".$modul->model." It has been updated now'],\n";
                $out .= "\t\t'DELETED' => ['action' => 'Deleted', 'description' => '".$modul->model." Deleted']\n";
                $out .= "\t],\n\n";
            }
        } else {
            $out .= "'__ArrayDate__'";
        }

        $out = trim($out);
        // $this->info($this->getPath($name));
        $stub = str_replace("'__ArrayDate__'", $out, $stub);
        
        return $stub;
    }

    /**
     * Determine if the class already exists.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function alreadyExists($name)
    {
        return $this->files->exists($this->getPath($name));
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        return config_path(str_replace('\\', '/', $name).'.php');
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        // return $this->files->get($this->getStub());
        
        $stub = $this->files->get($this->getStub());
        // return $stub;
        return $this->replaceNameStrings($stub);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [

        ];
    }
}
