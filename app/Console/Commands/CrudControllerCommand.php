<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 *  this is a command to create a crud controller.
 * 	It will called from CrudCommand.
 *
 *  @package Crud
 *  @author	Anirban Saha
 */
class CrudControllerCommand extends GeneratorCommand {
	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:controller
                            {name : The name of the controler.}
							{--table-name= : The name of the table.}
                            {--route-name= : Prefix of the route group.}';
   /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new crud controller.';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
		return app_path('Http/Library/Crud/Controller.stub');
    }
    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }
    /**
     * Build the model class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
		$controller_stub = $this->files->get($this->getStub());
		$model_name = studly_case(str_singular($this->option('table-name')));
 		$request_class = $model_name.'Request';
		$controller_name = $model_name.'Crud';
		$namespace = str_ireplace('\\'.$controller_name,'', $name);
 		$controller_stub = str_ireplace('@NAMESPACE', $namespace, $controller_stub);
 		$controller_stub = str_ireplace('@ROUTE_SLUG', $this->option('route-name'), $controller_stub);
 		$controller_stub = str_ireplace('@REQUEST_CLASS', $request_class, $controller_stub);
 		$controller_stub = str_ireplace('@CONTROLLER_NAME', $controller_name, $controller_stub);
 		$controller_stub = str_ireplace('@TABLE_NAME', $this->option('table-name'), $controller_stub);
 		$controller_stub = str_ireplace('@LABEL', $model_name, $controller_stub);
 		return $controller_stub;
    }
}
