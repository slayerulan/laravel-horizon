<?php

namespace App\Http\Controllers\admin;
use App\Http\Traits\PlayerRegistration;
use App\Http\Traits\DataSaver;
use App\Http\Requests\userRegistration;
use App\Http\Requests\AdminUpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Crud;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use DB;
use App\User;
use App\PlayerWallet;
use App\Country;
use App\Currency;
use App\Language;
use App\BetRule;

/**
 *  This is a command to fetch all players and save into database from admin panel.
 *
 *  @author Sourav Chowdhury
 */
class PlayerCrud extends Crud
{
    use PlayerRegistration,DataSaver;
  /**
   * name of the table . REQUIRED
   * @var string
   */
  public $table_name        = 'users';
  /**
   * route name that shold be used to create different action link. REQUIRED
   * @var string
   */
  public $route_slug        = 'admin-player-management-player-';
  /**
   * You can use RBAC to manage action button by crud. OPTIONAL
   * @var bool
   */
  public $use_rbac        = false;
  /**
   * You can customize you table coloumn.
   *  field name as key, label as value. only table field are acceptable. OPTIONAL
   * @var array
   */
   public $columns_list = ['username' => "Username", 'agent_id' => 'Agent', 'bet_rule_id' => "Bet Rule", 'status' => "Status", 'created_at' => "Created At"];
  /**
   * You can unset action button. 'view/edit/delete acceptable'. OPTIONAL
   * @var array
   */
  public $unset_actions_button  = ['edit','delete'];

  public $unset_coloumn = ['id','role_id','full_name','email','mobile','password','unique_code','otp','deleted_at','updated_at','updated_by'];

  Public  $unset_relation_coloumn  =  ['agent_id'];

  /**
   * This will display table data in view page in data table
   * @return view              load view page
   */
    public function show()
    {
        $this->page_title = 'Player List';
        $this->setAddLink(route('admin-player-management-add-player'));
        $this->setActionButton(__('Edit'),'waves-effect btn btn-warning','create','edit');
        $this->setActionButton(__('Delete'),'waves-effect player_delete_button btn btn-danger','delete_sweep','delete');
        $this->setRelation('bet_rule_id', 'bet_rules', 'title');
        $this->setRelation('agent_id', 'users', 'username');
        $data = $this->rendarShow();
        return view('admin.crud.show',$data);
    }
  /**
   * This will display a details for an id of this table
   * @param  integer  $id      id of selected row
   * @return view              load view page
   */
  public function view($id)
  {
    $this->page_title = 'View User';
    $this->setRelation('bet_rule_id', 'bet_rules', 'title');
    $this->setRelation('agent_id', 'users', 'username');
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
     * this function is for load add player page
     */
    public function getAddUser()
    {
        $this->setLeftSideBarData();
        $agents = array();
        $data = array();
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');


        $country = Country::orderBy('countries.name', 'asc')
                   ->get()
                   ->toArray();

        $language = Language::where('status', 'active')
                   ->get()
                   ->toArray();

        $currency = Currency::where('status', 'active')
                   ->get()
                   ->toArray();

        $loggedUser = User::find($this->user_id);

        $data['id'] = $loggedUser->id;
        $data['username'] = $loggedUser->username;
        array_push($agents,$data);


        if($role_id == 1)
        {
            $agents = User::where('role_id','!=',4)->where('status','active')->get()->toArray();
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
          $agentsAll = User::find($this->user_id)->children()->where('role_id','!=',4)->where('status','active')->get()->toArray();
          if($agentsAll)
          {
            if(!in_array($agentsAll[0]['id'], $data))
            {
                foreach($agentsAll as $allData)
                {
                    array_push($agents,$allData);
                }
            }
          }
        }

        if($this->role_id == 1)
        {
            $bet_rules = BetRule::whereNull('deleted_at')->get();
        }
        else
        {
            $bet_rules = BetRule::where('user_id', $this->user_id)
                    ->orwhere('is_default', 'yes')
                    ->get();
        }

        return view('admin/player/admin_add_player',['country' => $country, 'language' => $language,'currency' => $currency, 'agents' => $agents, 'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu, 'bet_rules' => $bet_rules]);
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
     * this function is for inset player data
     */
    public function postAddUser(userRegistration $request)
    {
            $request["role_id"] = 4;
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
            return redirect(route('admin-player-management-player-list'));
        //}
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
     * this function is for load player update page
     */
    public function EditPlayer($user_id)
    {
        $this->setLeftSideBarData();
        $agents = array();
        $data = array();

        $where = array('users.id' =>$user_id);
        $user = DB::table('users')
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->where($where)
            ->whereNull('deleted_at')
            ->get();

        // $country = Country::orderBy('countries.name', 'asc')
        //            ->get()
        //            ->toArray();

        // $language = Language::where('status', 'active')
        //            ->get()
        //            ->toArray();

        // $currency = Currency::where('status', 'active')
        //            ->get()
        //            ->toArray();

        $loggedUser = User::find($this->user_id);
        $data['id'] = $loggedUser->id;
        $data['username'] = $loggedUser->username;
        array_push($agents,$data);

        // if($loggedUser->role_id == 1)
        // {
        //     $agents = User::where('role_id','!=',4)->where('status','active')->get()->toArray();
        //     if($agents)
        //     {
        //       if(!in_array($agents[0]['id'], $data))
        //       {
        //         array_push($agents,$data);
        //       }
        //     }
        // }
        // else
        // {
        //   $agentsAll = User::find($this->user_id)->children()->where('role_id','!=',4)->where('status','active')->get()->toArray();
        //   if($agentsAll)
        //   {
        //     if(!in_array($agentsAll[0]['id'], $data))
        //     {
        //         foreach($agentsAll as $allData)
        //         {
        //             array_push($agents,$allData);
        //         }
        //     }
        //   }
        // }
        if($this->role_id == 1)
        {
            $bet_rules = BetRule::whereNull('deleted_at')->get();
        }
        else
        {
            $bet_rules = BetRule::where('user_id', $this->user_id)
                    ->orwhere('is_default', 'yes')
                    ->get();
        }
        $agent = [];
        if ($user[0]->agent_id != NULL) {
          $agent = User::find($user[0]->agent_id)->toArray();
        }

        return view('admin/player/admin_edit_player',['user_data' => $user, 'agent' => $agent, 'role_id' => $this->role_id, 'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu, 'bet_rules' => $bet_rules]);
    }
    /**
     * this function is for update player data
     * @param AdminUpdateUserRequest $request [description]
     */

    public function PostEditPlayer(AdminUpdateUserRequest $request)
    {

        $this->setLeftSideBarData();
        $user_id = $request->user_id;

        // $full_name = $request->full_name;
        // $email = $request->email;
        // $mobile = $request->mobile;
        // $profile_image = $request->profile_image;
        // $country_id = $request->country;
        // $address = $request->address;
        // $sex = $request->gender;
        // $language_id = $request->language;
        // $currency_id = $request->currency;
        $status = $request->status;
        $bet_rule_id = $request->bet_rule_id;
        $updated_at = date("Y-m-d H:i:s");
        $password = $request->input('password_confirmation');

        // $user = User::where('email',$email)->where('id', '!=', $user_id)->whereNull('deleted_at')->get()->toArray();
        // if($user)
        // {
        //     Session::flash('alert_class', 'danger');
        //     Session::flash('alert_msg', 'Email already exists!');
        //     return redirect(route('admin-player-management-player-edit',$request->user_id));
        // }

        if($request->agent_id)
        {
            $agent_id = $request->agent_id;
        }
        else
        {
            $agent_id = Session::get('user_id');
        }

        $user_update = array('bet_rule_id' => $bet_rule_id, 'status' => $status, 'updated_at' => $updated_at);

        // if($password)
        // {
        //   $user_update = array('agent_id' => $agent_id, 'bet_rule_id' => $bet_rule_id, 'full_name' => $full_name, 'email' => $email, 'password' => md5($password), 'mobile' => $mobile, 'status' => $status, 'updated_at' => $updated_at);
        // }
        // else
        // {
        //   $user_update = array('agent_id' => $agent_id, 'bet_rule_id' => $bet_rule_id, 'full_name' => $full_name, 'email' => $email, 'mobile' => $mobile, 'status' => $status, 'updated_at' => $updated_at);
        // }

        DB::table('users')
        ->where('id', $user_id)
        ->update($user_update);

        // if($request->file('profile_image'))
        // {
        //     $profile_image = $request->file('profile_image')->store('user_image');
        //     $user_profile_update = array('profile_image' => $profile_image, 'country_id' => $country_id, 'address' => $address, 'sex' => $sex, 'language_id' => $language_id, 'currency_id' => $currency_id, 'updated_at' => $updated_at);
        // }
        // else
        // {
        //     $user_profile_update = array('country_id' => $country_id, 'address' => $address, 'sex' => $sex, 'language_id' => $language_id, 'currency_id' => $currency_id, 'updated_at' => $updated_at);
        // }

        // DB::table('user_profiles')
        // ->where('user_id', $user_id)
        // ->update($user_profile_update);

        $this->log('Player Updated From Admin');

        Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Updated');
        return redirect(route('admin-player-management-player-list'));
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
   * this function is for delete player
   * @param [type] $id [description]
   */
  public function DeletePlayer($id)
  {
      $updated_at = date("Y-m-d H:i:s");
      $update_array = array('deleted_at' => $updated_at, 'updated_at' => $updated_at);

      DB::table('users')
        ->where('id', $id)
        ->update($update_array);

      $this->log('Player Deleted From Admin');

      Session::flash('alert_class', 'success');
      Session::flash('alert_msg', 'Successfully Deleted');
      return redirect(route('admin-player-management-player-list'));
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
          $where = "WHERE users.role_id =4 AND users.deleted_at is null";
        }
        else
        {
            $where = "WHERE users.role_id =4 AND users.deleted_at is null AND users.agent_id IN (SELECT DISTINCT users.id from users WHERE users.agent_id=$user_id) OR users.agent_id=$user_id AND users.role_id =4 AND users.deleted_at is null";
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
