<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Traits\PlayerRegistration;
use App\Http\Traits\DataSaver;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Crud;
use App\Http\Controllers\Controller;
use App\Http\Requests\userRegistration;
use App\Http\Requests\AdminUpdateUserRequest;
use DB;
use App\User;
use App\Country;
use App\Currency;
use App\Language;
use App\BetRule;
use App\RolePermission;
/**
 *  This is a command to fetch all sub agents and save into database from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class SubAgentCrud extends Crud
{
    use PlayerRegistration,DataSaver;
	/**
	 * name of the table . REQUIRED
	 * @var string
	 */
	public $table_name 				= 'users';
	/**
	 * route name that shold be used to create different action link. REQUIRED
	 * @var string
	 */
	public $route_slug 				= 'admin-sub-agent-management-sub-agent-';
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
	 public $columns_list			= ['agent_id' => "Parent",'full_name' => "Full Name",'username' => "Username" ,'email' => "Email",'mobile' => 'Mobile','status' => "Status"];
	/**
	 * You can unset action button. 'view/edit/delete acceptable'. OPTIONAL
	 * @var array
	 */
	public $unset_actions_button	= ['edit','delete'];

	public $unset_coloumn = ['id','role_id','bet_rule_id','password','unique_code','otp','deleted_at','created_at','updated_at','updated_by'];

	Public  $unset_relation_coloumn  =  ['agent_id'];

    /**
     * this function is for load sub agent add page
     * @return [type] [description]
     */
    public function getSubAgentAdd()
    {
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');

        $this->setLeftSideBarData();

        $where = array('role_permissions.role_id' =>$role_id,'role_permissions.can_add' =>1,'role_permissions.status' =>'active');
        $role_permissions = DB::table('role_permissions')
        ->leftJoin('roles', 'role_permissions.r_id', '=', 'roles.id')
        ->where($where)
        ->orderBy('role_permissions.role_id', 'asc')
        ->orderBy('roles.id', 'asc')
        ->get();


        $country = Country::orderBy('countries.name', 'asc')
                   ->get()
                   ->toArray();

        $language = Language::where('status', 'active')
                   ->get()
                   ->toArray();

        $currency = Currency::where('status', 'active')
                   ->get()
                   ->toArray();



        if($this->role_id == 1)
        {
            $bet_rules = BetRule::whereNull('deleted_at')->get();
        }
        else
        {
            $bet_rules = BetRule::where('user_id', $user_id)
                    ->orwhereNull('user_id')
                    ->whereNull('deleted_at')
                    ->get();
        }

        return view('admin/sub-agent/admin_add_sub_agent',['role_permissions' => $role_permissions, 'country' => $country, 'language' => $language,'currency' => $currency, 'bet_rules' => $bet_rules, 'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }
    /**
     * this page is for insert sub agent data
     */
    public function postSubAgentAdd(userRegistration $request)
    {
            if($request->agent_id)
            {
                $request["agent_id"] = $request->agent_id;
            }
            else
            {
                $request["agent_id"] = Session::get('user_id');
            }
            $this->registrationData($request);
            Session::flash('alert_class', 'success');
            Session::flash('alert_msg', 'Successfully Added');
            return redirect(route('admin-sub-agent-management-sub-agent-list'));
        //}
    }
    /**
     * this function is for load edit sub agent page
     */
    public function EditSubAgent($id)
    {
        $this->setLeftSideBarData();
        $agents = array();
        $data = array();
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');

        $where = array('users.id' =>$id);
        $user = DB::table('users')
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->where($where)
            ->whereNull('deleted_at')
            ->get();

        $r_id = $user[0]->role_id;
        $agent_id = $user[0]->agent_id;

        $loggedUser = User::find($user_id);
        $data['id'] = $loggedUser->id;
        $data['username'] = $loggedUser->username;
        array_push($agents,$data);

        if($role_id == 1)
        {
            $permittedRoleIds = RolePermission::select('role_id')->where('r_id',$r_id)->where('can_add',1)->where('status','active')->get()->pluck('role_id');
            $agents = User::whereIn('role_id',$permittedRoleIds)->where('status','active')->get()->toArray();
            if($agents)
            {
              if(!in_array($agents[0]['id'], $data))
              {
                array_push($agents,$data);
              }
            }
        }
        else
        {
            $permittedRoleIds = RolePermission::select('role_id')->where('r_id',$r_id)->where('can_add',1)->where('status','active')->get()->pluck('role_id');
            $agents = User::find($user_id)->children()->whereIn('role_id',$permittedRoleIds)->where('status','active')->get()->toArray();
            if($agents)
            {
              if(!in_array($agents[0]['id'], $data))
              {
                array_push($agents,$data);
              }
            }
        }

        $where = array('role_permissions.role_id' =>$role_id,'role_permissions.can_add' =>1,'role_permissions.status' =>'active');
        $role_permissions = DB::table('role_permissions')
        ->leftJoin('roles', 'role_permissions.r_id', '=', 'roles.id')
        ->where($where)
        ->orderBy('role_permissions.role_id', 'asc')
        ->orderBy('roles.id', 'asc')
        ->get();


        if($this->role_id == 1)
        {
            $bet_rules = BetRule::whereNull('deleted_at')->get();
        }
        else
        {
            $bet_rules = BetRule::where('user_id', $user_id)
                    ->orwhereNull('user_id')
                    ->whereNull('deleted_at')
                    ->get();
        }

        $country = Country::orderBy('countries.name', 'asc')
                   ->get()
                   ->toArray();

        $language = Language::where('status', 'active')
                   ->get()
                   ->toArray();

        $currency = Currency::where('status', 'active')
                   ->get()
                   ->toArray();

        return view('admin/sub-agent/admin_edit_sub_agent',['role_permissions' => $role_permissions, 'agents' => $agents, 'user_data' => $user, 'user_id' => $id,'bet_rules' => $bet_rules, 'country' => $country, 'language' => $language,'currency' => $currency, 'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }
    /**
     * this function is for update sub agent
     * @param AdminUpdateUserRequest $request [description]
     */
    public function PostEditSubAgent(AdminUpdateUserRequest $request)
    {
        $this->setLeftSideBarData();
        $user_id = $request->user_id;

        $full_name = $request->full_name;
        $email = $request->email;
        $mobile = $request->mobile;
        $profile_image = $request->profile_image;
        $country_id = $request->country;
        $address = $request->address;
        $role_id = $request->role_id;
        $sex = $request->gender;
        $language_id = $request->language;
        $currency_id = $request->currency;
        $updated_at = date("Y-m-d H:i:s");
        $bet_rule_id = $request->bet_rule_id;
        $password = $request->input('password_confirmation');

        if($request->agent_id)
        {
            $agent_id = $request->agent_id;
        }
        else
        {
            $agent_id = Session::get('user_id');
        }

        if($password)
        {
           $user_update = array('role_id' => $role_id, 'agent_id' => $agent_id, 'bet_rule_id' => $bet_rule_id, 'full_name' => $full_name, 'email' => $email, 'password' => md5($password), 'mobile' => $mobile, 'updated_at' => $updated_at);
        }
        else
        {
           $user_update = array('role_id' => $role_id, 'agent_id' => $agent_id, 'bet_rule_id' => $bet_rule_id, 'full_name' => $full_name, 'email' => $email, 'mobile' => $mobile, 'updated_at' => $updated_at);
        }


        DB::table('users')
        ->where('id', $user_id)
        ->update($user_update);

        if($request->file('profile_image'))
        {
            $profile_image = $request->file('profile_image')->store('user_image');
            $user_profile_update = array('profile_image' => $profile_image, 'country_id' => $country_id, 'address' => $address, 'sex' => $sex, 'language_id' => $language_id, 'currency_id' => $currency_id, 'updated_at' => $updated_at);
        }
        else
        {
            $user_profile_update = array('country_id' => $country_id, 'address' => $address, 'sex' => $sex, 'language_id' => $language_id, 'currency_id' => $currency_id, 'updated_at' => $updated_at);
        }

        DB::table('user_profiles')
        ->where('user_id', $user_id)
        ->update($user_profile_update);

        $this->log('Sub Agent Updated From Admin');

        Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Updated');
        return redirect(route('admin-sub-agent-management-sub-agent-list'));
    }
    /**
     * this function is for delete sub-agent
     * @param [type] $id [description]
     */
    public function DeleteSubAgent($id)
    {
        $updated_at = date("Y-m-d H:i:s");
        $update_array = array('deleted_at' => $updated_at, 'updated_at' => $updated_at);

        $players = User::where('role_id', 4)
                  ->where('agent_id', $id)
                  ->whereNull('deleted_at')
                  ->get();

        foreach ($players as $player_value)
        {
            $player_id = $player_value->id;

            DB::table('users')
            ->where('id', $player_id)
            ->update($update_array);
        }

        //$user = User::find($id);
        //$user->delete();
        DB::table('users')
        ->where('id', $id)
        ->update($update_array);

        $this->log('Sub Agent Deleted From Admin');

        Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Deleted');
        return redirect(route('admin-sub-agent-management-sub-agent-list'));
    }
    /**
     * this function is for get all agents regarding selected role id
     */
    public function AllAgents(Request $request)
	  {
	    $user_id = Session::get('user_id');
      $user_role_id = Session::get('role_id');
	    $r_id = $request['role_id'];

      $agents = array();
      $data = array();

        $loggedUser = User::find($user_id);
        $data['id'] = $loggedUser->id;
        $data['username'] = $loggedUser->username;
        array_push($agents,$data);


        if($user_role_id == 1)
        {
            $permittedRoleIds = RolePermission::select('role_id')->where('r_id',$r_id)->where('can_add',1)->where('status','active')->get()->pluck('role_id');
            $agents = User::whereIn('role_id',$permittedRoleIds)->where('status','active')->get()->toArray();
            if($agents)
            {
              if(!in_array($agents[0]['id'], $data))
              {
                array_push($agents,$data);
              }
            }
        }
        else
        {
            $permittedRoleIds = RolePermission::select('role_id')->where('r_id',$r_id)->where('can_add',1)->where('status','active')->get()->pluck('role_id');
            $agents = User::find($user_id)->children()->whereIn('role_id',$permittedRoleIds)->where('status','active')->get()->toArray();
            array_push($agents,$data);
        }


        echo '<option value="">-- Please select --</option>';
        foreach ($agents as $agents_data)
        {
            echo '<option value="'.$agents_data['id'].'">'.$agents_data['username'].'</option>';
        }

	}
	/**
	 * This will display table data in view page in data table
	 * @return view           	 load view page
	 */
    public function show()
    {
        $this->setRelation('agent_id', 'users', 'username');
    		$this->page_title = 'Agent List';;
    		$this->setAddLink(route('admin-sub-agent-management-sub-agent-add'));
    		$this->setActionButton(__('Edit'),'waves-effect btn btn-warning','create','edit');
    		$this->setActionButton(__('Delete'),'waves-effect sub_agent_delete_button btn btn-danger','delete_sweep','delete');
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
		$this->page_title = 'View Agent';
		$data = $this->rendarView($id);
		return view('admin.crud.view',$data);
	}
	/**
	 * This will load an insert form for current table
	 * @return view   load view page
	 */
	public function add()
	{
		$this->page_title = 'Add User';
		$data = $this->rendarAdd();
		return view('admin.crud.form',$data);
	}
	/**
	 * This will insert data into databse
	 * @param  UserRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function insert(UserRequest $request)
	{
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
		$this->page_title = 'Edit User';
		$data = $this->rendarEdit($id);
		return view('admin.crud.form',$data);
	}
	/**
	 * this will update a row
	 * @param  PlayerRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function update(PlayerRequest $request)
	{
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
	 * If you want to call any function for all, set here. by default crud will call this
	 * @return void        called by crud self
	 */
	public function callDefault()
	{
	    $this->unsetAdd();

		$user_id = $this->user_id;
		$role_id = $this->role_id;
		if($role_id == 1)
		{
			$where = "WHERE users.role_id !=4 AND  users.deleted_at is null";
		}
    else
    {
        $where = "WHERE users.role_id !=1 AND users.role_id !=4 AND users.deleted_at is null AND users.agent_id=$user_id";
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
