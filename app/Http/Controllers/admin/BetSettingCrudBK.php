<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Crud;
use App\Http\Controllers\Controller;
use App\Http\Requests\BetSettingRequest;
use App\Http\Traits\DataSaver;

/**
 *  This is a command to fetch all bet settings and save into database from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class BetSettingCrud extends Crud
{
	use DataSaver;
	/**
	 * name of the table . REQUIRED
	 * @var string
	 */
	public $table_name 				= 'bet_settings';
	/**
	 * route name that shold be used to create different action link. REQUIRED
	 * @var string
	 */
	public $route_slug 				= 'admin-sports-book-management-bet-settings-';
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
	//public $columns_list;
	/**
	 * You can unset action button. 'view/edit/delete acceptable'. OPTIONAL
	 * @var array
	 */
	public $unset_actions_button	= ['delete'];

	public $unset_coloumn = ['id','created_at','updated_at','updated_by'];

	/**
	 * This will display table data in view page in data table
	 * @return view           	 load view page
	 */
    public function show()
    {
		$this->page_title = 'Bet Setting List';
    	$data = $this->rendarShow();
		$data['table_field']['bookmaker_id'] = 'Default Bookmaker';
		return view('admin.crud.show',$data);
    }
	/**
	 * This will display a details for an id of this table
	 * @param  integer  $id      id of selected row
	 * @return view           	 load view page
	 */
	public function view($id)
	{
		$this->page_title = 'View Bet Setting';
		$data = $this->rendarView($id);
		$data['label_data']['bookmaker_id'] = 'Default Bookmaker';
		return view('admin.crud.view',$data);
	}
	/**
	 * This will load an insert form for current table
	 * @return view   load view page
	 */
	public function add()
	{
		$this->page_title = 'Add Bet Setting';
		$data = $this->rendarAdd();
		$data['input_list']['bookmaker_id']['field_label'] = 'Default Bookmaker';
		return view('admin.crud.form',$data);
	}
	/**
	 * This will insert data into databse
	 * @param  BetSettingRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function insert(BetSettingRequest $request)
	{
		$user_id = Session::get('user_id');
		$this->log('Bet Settings Added From Admin',$user_id);

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
		$this->page_title = 'Edit Bet Setting';
		$this->changeFieldType('pre_match_hide_minutes','number','Pre match hide minutes',null,null,'form-control','');
		$this->changeFieldType('max_day_place_bet','number','Max day to place bet',null,null,'form-control','');
		$data = $this->rendarEdit($id);
		$data['input_list']['bookmaker_id']['field_label'] = 'Default Bookmaker';
		return view('admin.crud.form',$data);
	}
	/**
	 * this will update a row
	 * @param  BetSettingRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function update(BetSettingRequest $request)
	{
		$user_id = Session::get('user_id');
		$this->log('Bet Settings Updated From Admin',$user_id);

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
		$user_id = Session::get('user_id');
		$this->log('Bet Settings Deleted From Admin',$user_id);

		$response = $this->deleteData($id);
		return redirect($response);
	}
	/**
	 * If you want to call any function for all, set here. by default crud will call this
	 * @return void        called by crud self
	 */
	public function callDefault()
	{
		// $this->setRelation('created_by', 'users', 'username');
		//$this->changeFieldType('role_id','muliselect','Role Name');
		//$this->addCallBackColoumn('field', 'label', 'callbackFunctionName');
		$this->unsetAdd();
	}
	/**
	 * this is a demo callback function, must send $value for default callback
	 * @param mixed $row_data  current row object/array
	 * @param mixed $value    callback field current value
	 * @param string $type     list/view/insert/update
	 */
	/*public function setStatus($row_data,$value,$type)
	{
		if($type =="list" || $type =="view"){
			$type = ['active'=> 'bg-green', 'inactive' => 'bg-red','1'=>'bg-green','0'=>'bg-red'];
			$value = isset($type[$value]) ? $type[$value] : 'bg-grey';
			return '<span class="badge '.$value.'"> '.$row_data->status.' </span>';
		}
		return $value;
	}*/
}
