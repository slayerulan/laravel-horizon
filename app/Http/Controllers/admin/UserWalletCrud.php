<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Crud;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerWalletRequest;
use App\Http\Traits\DataSaver;
use DB;
use App\User;
use App\UserWallet;

/**
 *  This is a command to fetch all player wallet details and save into database from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class UserWalletCrud extends Crud
{
	use DataSaver;
	/**
	 * name of the table . REQUIRED
	 * @var string
	 */
	public $table_name 				= 'user_wallets';
	/**
	 * route name that shold be used to create different action link. REQUIRED
	 * @var string
	 */
	public $route_slug 				= 'admin-wallet-management-';
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
	public $columns_list			= ['user_id' => "Player",'amount' => "Chips" ,'status' => "Status"];
	/**
	 * You can unset action button. 'view/edit/delete acceptable'. OPTIONAL
	 * @var array
	 */
	public $unset_actions_button	= ['delete'];

	public $unset_coloumn = ['id','deleted_at','created_by','created_at','updated_at','updated_by'];

	//Public  $unset_relation_coloumn  =  ['user_id'];

	/**
	 * This will display table data in view page in data table
	 * @return view           	 load view page
	 */
    public function show()
    {
			$this->page_title = 'Users Wallet List';
    	$data = $this->rendarShow();
			$data['table_field']['user_id'] = 'User';
			return view('admin.crud.show',$data);
    }
	/**
	 * This will display a details for an id of this table
	 * @param  integer  $id      id of selected row
	 * @return view           	 load view page
	 */
	public function view($id)
	{
		$this->page_title = 'View Users Wallet';
		$data = $this->rendarView($id);
		$data['label_data']['user_id'] = 'User';
		$data['label_data']['wallet_id'] = 'Wallet';
		return view('admin.crud.view',$data);
	}
	/**
	 * This will load an insert form for current table
	 * @return view   load view page
	 */
	public function add()
	{
		$this->page_title = 'Add Users Wallet';
		$data = $this->rendarAdd();
		$this->setLeftSideBarData();
        $users = array();

    $playerWalletUserIds = UserWallet::pluck('user_id')->all();
		$users = User::whereNotIn('id', $playerWalletUserIds)
                ->where('role_id',4)
                ->where('agent_id', $this->user_id)
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->get()
        		->toArray();

		return view('admin/player/admin_add_player_wallet',['users' => $users, 'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);

	}
	/**
	 * This will insert data into databse
	 * @param  PlayerWalletRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function insert(PlayerWalletRequest $request)
	{
				$player_wallet_data = array('user_id'=>$request->user_id,
                           'amount'=>$request->amount,
                           'status' => 'active',
						   				 	   'updated_by' => Session::get('user_id'),
                           'created_at' => date("Y-m-d H:i:s"));

	    	$player_wallet_id = DB::table('user_wallets')
                ->insertGetId($player_wallet_data);

				$user_id = Session::get('user_id');
				$this->log('Users Wallet Added From Admin',$user_id);

				Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Updated');
        return redirect(route('admin-player-management-wallet-management-list'));
	}
	/**
	 * this will load edit form
	 * @param  integer $id id of this table
	 * @return view     load edit form
	 */
	public function edit($id)
	{
		$this->setLeftSideBarData();
		$user_array = array();

		$player_wallet_data = UserWallet::find($id);

		$users = User::find($player_wallet_data->user_id);

		$this->page_title = 'Edit Players Wallet';
		$data = $this->rendarAdd();
		$this->setLeftSideBarData();

		return view('admin/wallet/admin_edit_wallet',['player_wallet_data' => $player_wallet_data,'users' => $users,'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
	}
	/**
	 * this will update a row
	 * @param  PlayerWalletRequest $request validated form request
	 * @return void                 redirect page
	 */
	public function update(PlayerWalletRequest $request)
	{
				$user_id = Session::get('user_id');

				if ($request->action == 'deposit')
				{
					  $amount = $request->wallet_balance + $request->amount;
				}
				else
				{
					  $amount = $request->wallet_balance - $request->amount;
				}
				if ($amount < 0)
				{
						Session::flash('alert_class', 'danger');
		        Session::flash('alert_msg', 'Wallet chips can not be less than available chips.');
		        return redirect(route('admin-wallet-management-edit', $request->player_wallet_id));
				}

				$user_wallets = UserWallet::find($request->player_wallet_id);
				$user_wallets->amount = $amount;
		    $user_wallets->status = 'active';
			  $user_wallets->updated_by = $user_id;
		    $user_wallets->updated_at = date("Y-m-d H:i:s");
				$user_wallets->save();

				$this->log('Users Wallet Updated From Admin',$user_id);

				Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Updated');
        return redirect(route('admin-wallet-management-list'));

	}
	/**
	 * this will delete a row
	 * @param  inetger $id id or row to be deleted
	 * @return void     redirect to list page
	 */
	public function delete($id)
	{
		$user_id = Session::get('user_id');
		$this->log('Users Wallet Deleted From Admin',$user_id);

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
		   $where = "WHERE user_wallets.deleted_at is null AND user_wallets.user_id IN (SELECT DISTINCT users.id from users WHERE users.role_id != 1 AND users.deleted_at is null)";
		  }
	    else
	    {
					$where = "WHERE user_wallets.deleted_at is null AND user_wallets.user_id IN (SELECT DISTINCT users.id from users WHERE users.agent_id=$user_id) OR A.agent_id=$user_id OR A.id=$user_id AND A.role_id !=1 AND A.deleted_at is null";
			}

			$this->additional_where   = $where;
			$this->setRelation('user_id', 'users', 'full_name');
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
