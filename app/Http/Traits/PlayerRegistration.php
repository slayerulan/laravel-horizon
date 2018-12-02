<?php
/**
 * This will contain all permission formula
 */

namespace App\Http\Traits;
use Illuminate\Http\Request;
use Session;
use DB;
use App\User;
use App\UserProfile;
use Mail;
use App\Mail\PlayerRegistrationMail;
use App\Mail\AgentRegistrationMail;


trait PlayerRegistration{

    public $url = false;
    public $username = false;
    public $password = false;

    public function registrationData( $request)
    {
        $role_id = $request->role_id;
        $full_name = $request->full_name;
        $username = $request->username;
        $email = $request->email;
        $mobile = $request->mobile;
        $gender = $request->gender;
        $address = $request->address;
        $agent_id = $request->agent_id;
        $bet_rule_id = $request->bet_rule_id;
        $country = $request->country;
        $language = $request->language;
        $currency = $request->currency;
        if($request->file('profile_image'))
        {
            $profile_image = $request->file('profile_image')->store('user_image');
        }
        else {
            $profile_image = '';
        }

		if($request->password){
			$password = $request->password;
		}else{
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+";
			$password = substr( str_shuffle( $chars ), 0, 8 );
		}

        $unique_code = rand(111111,999999);

        if($role_id ==4)
        {
            $status = 'inactive';
        }
        else {
            $status = 'active';
        }
        $user_data = array('role_id'=>$role_id,
                           'agent_id'=>$agent_id,
                           'bet_rule_id'=>$bet_rule_id,
                           'full_name' => $full_name,
                           'username' => $username,
                           'email' => $email,
                           'mobile' => $mobile,
                           'password' => md5($password),
                           'unique_code' => $unique_code,
                           'status' => $status,
                           'created_at' => date("Y-m-d H:i:s"));
                $user_id = DB::table('users')
                ->insertGetId($user_data);

                $this->log('New Registration From Admin');

        $user_profile_data = array('user_id'=>$user_id,
                             'profile_image'=>$profile_image,
                             'country_id'=>$country,
                             'address'=>$address,
                             'sex'=>$gender,
                             'language_id'=>$language,
                             'currency_id'=>$currency,
                             'created_at' => date("Y-m-d H:i:s"));
                $user_profiles_id = DB::table('user_profiles')
                ->insertGetId($user_profile_data);

        //$loggedInUser = Session::get('user_id');
        $wallet_data = array('user_id' => $user_id,
                     'amount' => 0,
                     'status' => 'active',
                     'created_at' => date("Y-m-d H:i:s"));

        $wallet_id = DB::table('user_wallets')
                ->insertGetId($wallet_data);

        if($user_profiles_id){

        if($role_id == 4){
            $this->url = url('active-my-account/'.$unique_code);
        }else{
            $this->url = '';
        }
        $this->full_name = $full_name;
        $this->username = $username;
        $this->password = $password;

        $to_email = $email;
            if($role_id == 4){
               Mail::to($to_email)->send(new PlayerRegistrationMail($this));
            }
            else {
                Mail::to($to_email)->queue(new AgentRegistrationMail($this));
            }
        }
    }

}
