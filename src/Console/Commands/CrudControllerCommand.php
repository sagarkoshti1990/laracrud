<?php

namespace Sagartakle\laracrud\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Sagartakle\laracrud\Helpers\Inflect;

class CrudControllerCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lara:controller';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lara:controller {name} {--option=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a bp CRUD controller';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);
        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'Controller.php';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers\Admin';
    }

    /**
     * Replace the table name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceNameStrings(&$stub, $name)
    {
        $table = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', str_replace($this->getNamespace($name).'\\', '', Inflect::pluralize($name)))), '_'));
        
        $stub = str_replace('__Class__', ucfirst(str_replace($this->getNamespace($name).'\\', '', Inflect::pluralize($name))), $stub);
        if($this->option('option') == "with-views") {
            $stub = str_replace('__ViewFilePathIndex__', 'admin.'.ucfirst(str_replace($this->getNamespace($name).'\\', '', $name)).'.index', $stub);
            $stub = str_replace('__ViewFilePathCreate__', 'admin.'.ucfirst(str_replace($this->getNamespace($name).'\\', '', $name)).'.create', $stub);
            $stub = str_replace('__ViewFilePathEdit__', 'admin.'.ucfirst(str_replace($this->getNamespace($name).'\\', '', $name)).'.edit', $stub);
            $stub = str_replace('__ViewFilePathShow__', 'admin.'.ucfirst(str_replace($this->getNamespace($name).'\\', '', $name)).'.show', $stub);
        } else {
            $stub = str_replace('__ViewFilePathIndex__', 'crud.index', $stub);
            $stub = str_replace('__ViewFilePathCreate__', 'crud.form', $stub);
            $stub = str_replace('__ViewFilePathEdit__', 'crud.form', $stub);
            $stub = str_replace('__ViewFilePathShow__', 'crud.show', $stub);
        }
        $stub = str_replace('__ModelName__', ucfirst(str_replace($this->getNamespace($name).'\\', '', Inflect::singularize($name))), $stub);
        $stub = str_replace('__smallPlural__', strtolower(str_replace($this->getNamespace($table).'\\', '', Inflect::pluralize($table))), $stub);
        $stub = str_replace('__smallSingular__', strtolower(str_replace($this->getNamespace($table).'\\', '', Inflect::singularize($table))), $stub);

        return $this;
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName) {
        return false;
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
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceNameStrings($stub, $name)->replaceClass($stub, $name);
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
