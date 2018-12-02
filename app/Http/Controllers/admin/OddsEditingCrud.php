<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Crud;
use App\Http\Controllers\Controller;
use App\Http\Requests\OddsEditingRequest;
use App\Http\Traits\DataSaver;
use DB;
use App\OddsEditing;
use App\User;
use App\FeedModel\Sport;
use App\FeedModel\Bookmaker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class OddsEditingCrud extends Crud
{
	use DataSaver;
	/**
	 * name of the table . REQUIRED
	 * @var string
	 */
	public $table_name 				= 'odds_editings';
	/**
	 * route name that shold be used to create different action link. REQUIRED
	 * @var string
	 */
	public $route_slug 				= 'admin-sports-book-management-odds-editing-';
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
	//public $columns_list = ['user_id' => "Agent", 'type' => "Type", 'sport_id' => "Sport", 'bookmaker_id' => 'Bookmaker', 'percentage' => "Percentage", 'action_type' => "Action Type", 'status' => "Status"];
	/**
	 * You can unset action button. 'view/edit/delete acceptable'. OPTIONAL
	 * @var array
	 */
	public $unset_actions_button	= ['delete'];

	public $unset_relation_coloumn	= ['user_id'];

	public $unset_coloumn = ['id','created_at','updated_at','updated_by'];

	/**
	 * This will display table data in view page in data table
	 * @return view           	 load view page
	 */
    public function show()
    {
		$this->page_title = 'Odds Juice List';
	  	$role_id = Session::get('role_id');
	  	if ($role_id != 1) {
	  		array_push($this->unset_coloumn, 'is_default');
	  	}
	  	$this->setRelation('user_id', 'users', 'username');
		$data = $this->rendarShow();
		$data['table_field']['user_id'] = 'Agent';
		$data['table_field']['sport_id'] = 'Sport';
		$data['table_field']['bookmaker_id'] = 'Bookmaker';
		$data['table_field']['action_type'] = 'Action Type';
		$this->setActionButton(__('Edit'),'waves-effect btn btn-warning','create','edit');
		return view('admin.crud.show',$data);
    }
	/**
	 * This will display a details for an id of this table
	 * @param  integer  $id      id of selected row
	 * @return view           	 load view page
	 */
	public function view($id)
	{
		$this->page_title = 'View Odds Juice';
		$data = $this->rendarView($id);
		$data['label_data']['user_id'] = 'Agent';
		$data['label_data']['sport_id'] = 'Sport';
		$data['label_data']['bookmaker_id'] = 'Bookmaker';
		$data['label_data']['status'] = 'Status';
		return view('admin.crud.view',$data);
	}
	/**
	 * This will load an insert form for current table
	 * @return view   load view page
	 */
	public function add()
	{
		$users = array();
		$user_id = Session::get('user_id');
	    $role_id = Session::get('role_id');

	    $this->setLeftSideBarData();

		$sports = Sport::all();
		$bookmakers = Bookmaker::all();

		if($role_id == 1)
		{
			$all_users = User::where('role_id','!=',4)
								->whereNull('deleted_at')->get()->toArray();
			array_push($users,$all_users);
		}
		else
		{
			$loggedInUser = User::where('id',$user_id)->get()->toArray();
			array_push($users,$loggedInUser);
			$all_users = User::where('role_id','!=',1)
								->where('role_id','!=',4)
								->where('agent_id',$user_id)
								->whereNull('deleted_at')->get()->toArray();
			if($all_users)
			{
					array_push($users[0],$all_users[0]);
			}
		}

		if($this->role_id == 1){
			$show_default_field = true;
		}
		else{
			$show_default_field = false;
		}

		return view('admin.sports-book.odds-editing.add',['loggedInUser' => $user_id, 'sports' => $sports, 'bookmakers' => $bookmakers, 'users' => $users, 'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu, 'show_default_field' => $show_default_field]);
	}
	/**
	 * This will insert data into databse
	 * @param  OddsEditingRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function insert(OddsEditingRequest $request)
	{
		$user_id = '';
		$sport_id = '';
		$bookmaker_id = '';

		$type = $request->type;
		if(isset($request->sport_id))
		{
			$sport_id = $request->sport_id;
		}
		if(isset($request->bookmaker_id))
		{
			$bookmaker_id = $request->bookmaker_id;
		}
		if(isset($request->user_id))
		{
			$user_id = $request->user_id;
		}

		$percentage = $request->percentage;
		$action_type = $request->action_type;
		$status = $request->status;
		$this->forgetCache();
		if($sport_id != '')
		{
			if (isset($request->is_default) && $request->is_default == 'yes' && !empty(OddsEditing::where('sport_id',$request->sport_id)->where('status',$request->status)->where('is_default',$request->is_default)->first())) {
				Session::flash('alert_class', 'danger');
		      	Session::flash('alert_msg', 'There is already a default value for the combination');
		      	return redirect(route('admin-sports-book-management-odds-editing-add'));
			}

			$oddsEditing = OddsEditing::where('user_id',$user_id)
										->where('type',$type)
										->where('sport_id',$sport_id)
										->first();
			if($oddsEditing)
			{
				Session::flash('alert_class', 'danger');
          		Session::flash('alert_msg', 'Selected sport related data is already exists!');
          		return redirect(route('admin-sports-book-management-odds-editing-add'));
			}
			else
			{
				$oddsEditing = new OddsEditing;
				$oddsEditing->user_id = $user_id;
				$oddsEditing->type = $request->type;
				$oddsEditing->sport_id = $sport_id;
				$oddsEditing->percentage = $percentage;
				$oddsEditing->action_type = $action_type;
				if (isset($request->is_default)) {
					$oddsEditing->is_default = $request->is_default;
				}
				$oddsEditing->status = $status;
				$oddsEditing->save();

				Session::flash('alert_class', 'success');
          		Session::flash('alert_msg', 'Successfully Added');
          		return redirect(route('admin-sports-book-management-odds-editing-list'));
			}
		}

		if($bookmaker_id != '')
		{
			if (isset($request->is_default) && $request->is_default == 'yes' &&!empty(OddsEditing::where('bookmaker_id',$request->bookmaker_id)->where('status',$request->status)->where('is_default',$request->is_default)->first())) {
				Session::flash('alert_class', 'danger');
		      	Session::flash('alert_msg', 'There is already a default value for the combination');
		      	return redirect(route('admin-sports-book-management-odds-editing-add'));
			}

			$oddsEditing = OddsEditing::where('user_id',$user_id)
										->where('type',$type)
										->where('bookmaker_id',$bookmaker_id)
										->first();
			if($oddsEditing)
			{
				Session::flash('alert_class', 'danger');
		        Session::flash('alert_msg', 'Selected bookmaker related data is already exists!');
		        return redirect(route('admin-sports-book-management-odds-editing-add'));
			}
			else
			{
				$oddsEditing = new OddsEditing;
				$oddsEditing->user_id = $user_id;
				$oddsEditing->type = $request->type;
				$oddsEditing->bookmaker_id = $bookmaker_id;
				$oddsEditing->percentage = $percentage;
				$oddsEditing->action_type = $action_type;
				if (isset($request->is_default)) {
					$oddsEditing->is_default = $request->is_default;
				}
				$oddsEditing->status = $status;
				$oddsEditing->save();

				Session::flash('alert_class', 'success');
          		Session::flash('alert_msg', 'Successfully Added');
          		return redirect(route('admin-sports-book-management-odds-editing-list'));
			}
		}
	}
	/**
	 * this will load edit form
	 * @param  integer $id id of this table
	 * @return view     load edit form
	 */
	public function edit($id)
	{
		$users = array();
		$user_id = Session::get('user_id');
	    $role_id = Session::get('role_id');

	    $this->setLeftSideBarData();

		$oddsEditingData = OddsEditing::where('id',$id)->first();
		$sports = Sport::all();
		$bookmakers = Bookmaker::all();

		if($role_id == 1) {
			$all_users = User::where('role_id','!=',4)
								->whereNull('deleted_at')->get()->toArray();
			array_push($users,$all_users);
		}
		else{
			$loggedInUser = User::where('id',$user_id)->get()->toArray();
			array_push($users,$loggedInUser);
			$all_users = User::where('role_id','!=',1)
								->where('role_id','!=',4)
								->where('agent_id',$user_id)
								->whereNull('deleted_at')->get()->toArray();

			if($all_users) {
				array_push($users[0],$all_users[0]);
			}
		}

		if($this->role_id == 1){
			$show_default_field = true;
		}
		else{
			$show_default_field = false;
		}
		return view('admin.sports-book.odds-editing.edit',['oddsEditingData' => $oddsEditingData, 'sports' => $sports, 'bookmakers' => $bookmakers, 'users' => $users, 'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu, 'show_default_field' => $show_default_field]);
	}
	/**
	 * this will update a row
	 * @param  OddsEditingRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function update(request $request)
	{
		if ($request->editing_type == 'Sport') {
			if (isset($request->is_default) && $request->is_default == 'yes' && !empty(OddsEditing::where('sport_id',$request->sports_id)->where('status',$request->status)->where('is_default',$request->is_default)->where('id', '!=', $request->id)->first())) {
				Session::flash('alert_class', 'danger');
		      	Session::flash('alert_msg', 'There is already a default value for the combination');
		      	return redirect(route('admin-sports-book-management-odds-editing-edit',$request->id));
			}
		}
		else{
			if (isset($request->is_default) && $request->is_default == 'yes' && !empty(OddsEditing::where('bookmaker_id',$request->bookmakers_id)->where('status',$request->status)->where('is_default',$request->is_default)->where('id', '!=', $request->id)->first())) {
				Session::flash('alert_class', 'danger');
		      	Session::flash('alert_msg', 'There is already a default value for the combination');
		      	return redirect(route('admin-sports-book-management-odds-editing-edit',$request->id));
			}
		}
		if (isset($request->status)) {
			$this->validate($request, [
				'percentage'  	=> 'required|numeric',
		        'action_type'  	=> 'required',
		        'status'  		=> 'required',
			]);
		}
		else{
			$this->validate($request, [
				'percentage'  	=> 'required|numeric',
		        'action_type'  	=> 'required',
			]);
		}

		$OddsEditing = OddsEditing::find($request->id);
		$OddsEditing->percentage = $request->percentage;
		$OddsEditing->action_type = $request->action_type;
		if (isset($request->is_default)) {
			$OddsEditing->is_default = $request->is_default;
		}
		if (isset($request->status)) {
			$OddsEditing->status = $request->status;
		}
		$OddsEditing->save();

		Session::flash('alert_class', 'success');
      	Session::flash('alert_msg', 'Successfully Updated');
      	$this->forgetCache();
      	return redirect(route('admin-sports-book-management-odds-editing-list'));
	}
	/**
	 * this will delete a row
	 * @param  inetger $id id or row to be deleted
	 * @return void     redirect to list page
	 */
	public function delete($id)
	{
		$response = $this->deleteData($id);
		$this->forgetCache();
		return redirect($response);
	}
	/**
	 * removes all caches from the server
	 */
	public function forgetCache()
	{
		Cache::flush();
	}
	/**
	 * If you want to call any function for all, set here. by default crud will call this
	 * @return void        called by crud self
	 */
	public function callDefault()
	{
			$user_id = $this->user_id;
			$role_id = $this->role_id;
	    if($role_id == 1)
			{
				$where = "WHERE A.deleted_at is null";
			}
			else
			{
				$where = "WHERE odds_editings.user_id =$user_id AND role_id !=4 OR agent_id IN (SELECT DISTINCT id from users WHERE agent_id=$user_id) OR agent_id=$user_id AND A.deleted_at is null";
			}
			$this->additional_where   = $where;
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
