<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Session;
use App\Http\Controllers\Controller;
use App\Http\Traits\PlayerRegistration;

use App\Http\Controllers\admin\AdminBaseController;
use App\Http\Requests\userRegistration;
use App\Http\Requests\AdminUpdateUserRequest;
use DB;
use App\User;
use App\PlayerWallet;
use App\Country;
use App\Currency;
use App\Language;
use App\BetRule;
use App\SupportTicket\SupportTicket;
use App\Notification;
use App\SupportTicket\StMessage;

/**
 *  This is a command to register & update agent,player from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class Registration extends AdminBaseController
{
    use PlayerRegistration;

    /**
     * this function is for load add player page
     */
    public function getAddUser()
    {
        $this->setLeftSideBarData();
        $master_agent = '';
        $basic_agent = '';

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
            $master_agent = User::where('role_id', 2)
                   ->where('status', 'active')
                   ->whereNull('deleted_at')
                   ->get()
                   ->toArray();
        }
        else if($this->role_id == 2)
        {
            $basic_agent = User::where('role_id', 3)
                   ->where('agent_id', $this->user_id)
                   ->where('status', 'active')
                   ->whereNull('deleted_at')
                   ->get()
                   ->toArray();
        }

        return view('admin/player/admin_add_player',['country' => $country, 'language' => $language,'currency' => $currency, 'role_id' => $this->role_id, 'master_agent' => $master_agent, 'basic_agent' => $basic_agent, 'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }
    /**
     * this function is for get basic agents by master agent through ajax
     */
    public function getBasicAgents(Request $request)
    {
        $basic_agent = User::where('role_id', 3)
                   ->where('agent_id', $request->id)
                   ->whereNull('deleted_at')
                   ->where('status', 'active')
                   ->get()
                   ->toArray();
        echo '<option value="">-- Please select --</option>';
        foreach ($basic_agent as $basic_agent_data)
        {
            echo '<option value="'.$basic_agent_data['id'].'">'.$basic_agent_data['username'].'</option>';
        }
    }

    public function getBetRules(Request $request)
    {
        $bet_rules = BetRule::where('user_id', $request->id)
                   ->orwhereNull('user_id')
                   ->whereNull('deleted_at')
                   ->get()
                   ->toArray();

        echo '<option value="">-- Please select --</option>';
        foreach ($bet_rules as $bet_rules_data)
        {
            echo '<option value="'.$bet_rules_data['id'].'">'.$bet_rules_data['title'].'</option>';
        }
    }
    /**
     * this function is for get players by basic agent through ajax
     */
    public function getPlayers(Request $request)
    {
        $playerWalletUserIds = PlayerWallet::pluck('user_id')->all();
        $players = User::whereNotIn('id', $playerWalletUserIds)
                ->where('role_id',4)
                ->where('agent_id', $request->id)
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->get();

        echo '<option value="">-- Please select --</option>';
        foreach ($players as $players_data)
        {
            echo '<option value="'.$players_data->id.'">'.$players_data->username.'</option>';
        }
    }
    /**
     * this function is for inset player data
     */
    public function postAddUser(userRegistration $request)
    {
        $request["role_id"] = 4;
        $request["bet_rule_id"] = 'NULL';
        if($request->agent_id)
        {
            $request["agent_id"] = $request->agent_id;
        }
        else {
            $request["agent_id"] = Session::get('user_id');
        }
        $this->registrationData($request);
        Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Added');
        return redirect(route('admin-player-management-player-list'));
    }

    /**
     * this function is for load player update page
     */
    public function EditPlayer($user_id)
    {
        $this->setLeftSideBarData();

        $where = array('users.id' =>$user_id);
        $user = DB::table('users')
            ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->where($where)
            ->whereNull('deleted_at')
            ->get();

        $master_agent = '';
        $basic_agent = '';
        $master_agent_id = '';

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
            $master_agent = User::where('role_id', 2)
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->get()
                ->toArray();

            if($user[0]->agent_id){
                $agent_id = $user[0]->agent_id;
                $agent_query = User::find($agent_id);
                $master_agent_id = $agent_query->agent_id;

                $basic_agent = User::where('role_id', 3)
                  ->where('agent_id', $master_agent_id)
                  ->where('status', 'active')
                  ->whereNull('deleted_at')
                  ->get()
                  ->toArray();
            }
        }
        else if($this->role_id == 2)
        {
           $basic_agent = User::where('role_id', 3)
                  ->where('agent_id', $this->user_id)
                  ->where('status', 'active')
                  ->whereNull('deleted_at')
                  ->get()
                  ->toArray();
        }

        return view('admin/player/admin_edit_player',['user_data' => $user, 'master_agent_id' => $master_agent_id, 'master_agent' => $master_agent, 'basic_agent' => $basic_agent, 'country' => $country, 'language' => $language,'currency' => $currency, 'role_id' => $this->role_id, 'profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu]);
    }
    /**
     * this function is for update player data
     * @param AdminUpdateUserRequest $request [description]
     */
    public function PostEditPlayer(AdminUpdateUserRequest $request)
    {
        $this->setLeftSideBarData();
        $user_id = $request->user_id;

        $full_name = $request->full_name;
        $email = $request->email;
        $mobile = $request->mobile;
        $profile_image = $request->profile_image;
        $country_id = $request->country;
        $address = $request->address;
        $sex = $request->gender;
        $language_id = $request->language;
        $currency_id = $request->currency;
        $status = $request->status;
        $updated_at = date("Y-m-d H:i:s");

        if($request->agent_id)
        {
            $agent_id = $request->agent_id;
        }
        else
        {
            $agent_id = Session::get('user_id');
        }

        $user_update = array('agent_id' => $agent_id,'full_name' => $full_name, 'email' => $email, 'mobile' => $mobile, 'status' => $status, 'updated_at' => $updated_at);

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

        $this->log('Player Updated From Admin');

        Session::flash('alert_class', 'success');
        Session::flash('alert_msg', 'Successfully Updated');
        return redirect(route('admin-player-management-player-list'));
    }

    /**
     * this function is for get agents by role id through ajax
     */
    public function getUsers(Request $request)
    {
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');

        if($role_id == 1)
        {
        	$players = User::where('role_id',4)
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->get();
		    }
        else
        {
          $players = User::where('role_id',4)
                ->where('agent_id', $user_id)
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->get();
        }

        echo '<option value="">-- Please select --</option>';
        foreach ($players as $players_data)
        {
            echo '<option value="'.$players_data['id'].'">'.$players_data['username'].'</option>';
        }
    }
    /**
     * this function is for get admin & agents by player id through ajax
     */
    public function getAgents(Request $request)
    {
        $users = array();
        $id = $request->id;

        $users = checkParentIds($id); //checkParentIds defined in helpers
        echo '<option value="">-- Please select --</option>';
        foreach ($users as $users_data)
        {
            echo '<option value="'.$users_data->id.'">'.$users_data->username.'</option>';
        }
    }
    public function notificationGet()
    {
        return Notification::all();
    }

    /**
	 *  this function is for get total no of unread support tickets
	 */
    public function getUnreadSupportTtickets()
    {
        $user_id = Session::get('user_id');
        $unread = 0;

        if(Session::get('role_id') == 1)
        {
            $StMessages = StMessage::where('sender','!=',$user_id)->where('state','not seen')->get();
        }
        else
        {
            $StMessages = DB::table('st_messages')
                ->where('st_messages.sender','!=',$user_id)
                ->where('support_tickets.allocate_to',$user_id)
                ->where('state','not seen')
          			->leftJoin('support_tickets', 'st_messages.ticket_id', '=', 'support_tickets.id')
          			->get();
        }
        $unread = count($StMessages);
        echo $unread;
    }
    /**
     * this function is for load coming soon page
     * @return [type] [description]
     */
    public function getPlayersActivity()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getPlayersWalletManagement()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getMoneyLine()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getOddsEditing()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getLeagueAndMatchSettings()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getStakeLimitation()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getAddContentPage()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getContentPageList()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getDepositReport()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getWithdrawalReport()
    {
        return redirect(route('admin-coming-soon'));
    }
    public function getViewTickets()
    {
        return redirect(route('admin-coming-soon'));
    }
}
