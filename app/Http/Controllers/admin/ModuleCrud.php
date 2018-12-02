<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Crud;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleRequest;

class ModuleCrud extends Crud
{
	/**
	 * name of the table . REQUIRED
	 * @var string
	 */
	public $table_name 				= 'modules';
	/**
	 * route name that shold be used to create different action link. REQUIRED
	 * @var string
	 */
	public $route_slug 				= 'admin-settings-module-management-';
	/**
	 * You can use RBAC to manage action button by crud. OPTIONAL
	 * @var bool
	 */
	public $use_rbac				= false;
	/**
	 * You can customize you table coloumn.
	 *  field name as key, label as value. only table field are acceptable. OPTIONAL
	 * @var array
	 */
	public $columns_list			= ['title' => 'Menu','slug_name' => 'Slug name', 'parent_id' => 'Parent Module', 'is_group' => 'Is Group', 'icon' => 'Icon', 'rank' => 'Rank', 'status' => 'Status'];
	/**
	 * You can unset action button. 'view/edit/delete acceptable'. OPTIONAL
	 * @var array
	 */
	public $unset_actions_button	= [];

	public $unset_coloumn 			= ['id','created_at','updated_at','updated_by','created_by'];
	public $unset_relation_coloumn 	= ['parent_id'];
	public $additional_where 		= 'ORDER BY rank';
	/**
	 * This will display table data in view page in data table
	 * @return view           	 load view page
	 */
    public function show()
    {
		  $this->page_title = 'Module List';
    	$data = $this->rendarShow();
		  return view('admin.crud.show',$data);
    }
	/**
	 * This will display a details for an id of this table
	 * @param  integer  $id      id of selected row
	 * @return view           	 load view page
	 */
	public function view($id)
	{
		$this->page_title = 'View Module';
		$data = $this->rendarView($id);
		$data['label_data']['parent_id'] = 'Parent Module';
		return view('admin.crud.view',$data);
	}
	/**
	 * This will load an insert form for current table
	 * @return view   load view page
	 */
	public function add()
	{
		$this->page_title = 'Add Module';
		$this->setRelation('parent_id', 'modules', 'title');
		$data = $this->rendarAdd();
		$data['input_list']['parent_id']['field_label'] = 'Parent Module';
		return view('admin.crud.form',$data);
	}
	/**
	 * This will insert data into databse
	 * @param  ModuleRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function insert(ModuleRequest $request)
	{
		$this->unset_coloumn 			= ['id','created_at','updated_at','updated_by'];
		$this->addCallBackColoumn('created_by', 'created_by', 'setCreatedBy');
		$response = $this->insertData($request);
		return redirect($response);
	}
	/**
	 * this will load edit form
	 * @param  integer $id id of this table
	 * @return view     load edit form
	 */
	public function edit($id)
	{
		$this->page_title = 'Edit Module';
        $this->setRelation('parent_id', 'modules', 'title');
		$data = $this->rendarEdit($id);

		$parent_id = $data['input_list']['parent_id']['default_value'];
		$data['input_list']['parent_id']['field_label'] = 'Parent Module';

		return view('admin.crud.form',$data);
	}
	/**
	 * this will update a row
	 * @param  ModuleRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function update(ModuleRequest $request)
	{
		$this->unset_coloumn 			= ['id','created_at','created_by','updated_at'];
		$this->addCallBackColoumn('updated_by', 'updated_by', 'setUpdatedBy');
		$response = $this->updateData($request);
		return redirect($response);
	}
	/**
	 * this will delete a row
	 * @param  inetger $id id or row to be deleted
	 * @return void     redirect to list page
	 */
	public function delete($id)
	{
		$response = $this->deleteData($id);
		return redirect($response);
	}
	/**
	 * this is a demo callback function, must send $value for default callback
	 * @param mixed $row_data  current row object/array
	 * @param mixed $value    callback field current value
	 * @param string $type     list/view/insert/update
	 */
	public function setCreatedBy($row_data,$value,$type)
	{
		if($type =="insert"){
			return Session::get('user_id');
		}
		return $value;
	}
	public function setUpdatedBy($row_data,$value,$type)
	{
		if($type =="update"){
			return Session::get('user_id');
		}
		return $value;
	}
}
