<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

/**
 *  this is a command to create a crud for a table.
 * 	It will make model, controller, request and append route group.
 *
 *  @package Crud
 *  @author	Anirban Saha
 */
class CrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {table_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will create crud for a table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table_name = $this->argument('table_name');
		$model_name = studly_case(str_singular($table_name));
		$request_class = $model_name.'Request';
		$controller_name = $model_name.'Crud';
		$create_model = $this->ask('Do you want to create model as App\\'.$model_name.'?');
		if($create_model == "yes"){
			$exitCode = Artisan::call('make:model',['name'=>$model_name]);
			 $this->info('Model created..');
		}
		$exitCode = Artisan::call('make:request',['name'=>$request_class]);
		$this->info('Validation Request Class created..');
		$namespace_path = $this->ask('Enter subdirectory after Controller if exist else "/"');
 		if($namespace_path != "/"){
 			$namespace = $namespace_path.'/'.$model_name.'Crud';
 		}else{
 			$namespace = $model_name.'Crud';
 		}
 		$route_slug = $this->ask('Enter Route group name');
		$this->call('crud:controller', ['name' => $namespace, '--table-name' => $table_name,'--route-name' => $route_slug]);
		$routeFile = base_path('routes/web.php');
		$isAdded = File::append($routeFile,$this->addRoutes($route_slug,$controller_name,str_singular($table_name)));
		$this->info('Route group added.');
	}
	public function addRoutes($route_slug,$controller_name,$prefix)
	{
		return
		"Route::group(['as' => '".$route_slug."','prefix' => '".$prefix."'],function(){
			Route::get('list',['as'=>'list','uses'=>'".$controller_name."@show']);
			Route::get('view/{id?}',['as'=>'view','uses'=>'".$controller_name."@view']);
			Route::get('add',['as'=>'add','uses'=>'".$controller_name."@add']);
			Route::post('insert',['as'=>'insert','uses'=>'".$controller_name."@insert']);
			Route::get('edit/{id?}',['as'=>'edit','uses'=>'".$controller_name."@edit']);
			Route::post('update',['as'=>'update','uses'=>'".$controller_name."@update']);
			Route::get('delete/{id?}',['as'=>'delete','uses'=>'".$controller_name."@delete']);
		});";

	}
}
