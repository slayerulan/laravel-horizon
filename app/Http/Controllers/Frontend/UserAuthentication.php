<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\FrontendLoginRequest;
use App\Http\Requests\FrontendRegistrationRequest;
use App\Http\Requests\FrontendChangePasswordRequest;
use App\Http\Controllers\Controller;
use App\UserProfile;
use App\User;
use App\Language;
use App\Currency;
use App\Country;
use App\Mail\FrontendForgetPasswordMail;

/**
 *  this is frontend authentication controller.
 *  this will manage registration, login, logout etc
 *
 *  @author Anirban Saha
 */
class UserAuthentication extends Controller
{

	/**
	 * This will load registration form
	 * @return html	 registration form
	 */
    public function index()
    {
		$data = Cache::remember('registration_form_details', 60*24, function () {
			$data['country'] = Country::orderBy('countries.name', 'asc')
	                   ->get()
	                   ->toArray();
	        $data['language'] = Language::where('status', 'active')
	                   ->get()
	                   ->toArray();
	        $data['currency'] = Currency::where('status', 'active')
	                   ->get()
	                   ->toArray();
			return $data;
		});
    	return view('frontend.user.registration',$data);
    }

    /**
     * gets a authentication token from Conflux and hit a api to get user details from conflux
     * @param  Request     $request     authentication token from conflux
     * @return html           redirects to the apex site
     */
    // public function postAuthentication(Request $request) {
    //     $token = $request->access_token;
    //     Session::put('auth_token',$token);
    //     return redirect()->route('front-home');
    // }
    
	/**
	 * This will register a new user
	 * @param  FrontendRegistrationRequest $request validated request data
	 * @return avoid                               redirect to login page
	 */
	public function postRegistration(FrontendRegistrationRequest $request)
	{
		$unique_code = str_random(50);		// custom function defined in helpers.php
		$request->role_id = 4;
		$user = new User();
		$user->role_id 		= 4;
		$user->full_name 	= $request->full_name;
		$user->username 	= $request->username;
		$user->email 		= $request->email;
		$user->mobile 		= $request->mobile;
		$user->password 	= md5($request->password);
		$user->unique_code 	= $unique_code;
		$user->status 		= 'Inactive';
		$user->save();
		if($request->file('profile_image')) {
            $profile_image = $request->file('profile_image')->store('user_image');
        }else {
            $profile_image = null;
        }
		$user_profile = new UserProfile();
		$user_profile->user_id			= $user->id;
		$user_profile->profile_image	= $profile_image;
		$user_profile->country_id		= $request->country;
		$user_profile->address			= $request->address;
		$user_profile->sex				= $request->gender;
		$user_profile->language_id		= $request->language;
		$user_profile->currency_id		= $request->currency;
		$user_profile->save();
		$user->wallets()->create();
		$this->log('New Registration',$user->id);
		$this->setFlashAlert('success',__('registration.Successfully Added'));
        return redirect(route('front-get-registration'));
	}
	/**
	 * this will active an account by $token
	 * @param  string $token unique code of user table
	 * @return void        redirect to login page
	 */
	public function getActiveMyAccount($token)
	{
		$user	  	= User::where('unique_code',$token)
							->where('status','inactive')
							->where('role_id',4)
							->first();
		if(isset($user->id)){
			$user_details = User::find($user->id);
			$user_details->status = 'active';
			$user_details->save();
			$this->log('Activate Account',$user->id);
			$this->setFlashAlert('success',__('registration.Account activated. Please Login'));
		}else {
			$this->setFlashAlert('danger',__('registration.Invalid Link'));
		}
		return redirect(route('front-get-login'));
	}
	/**
	 * this will load log in page
	 * @return html	 load view page
	 */
	public function getLogin()
	{
		if(Session::get('user_details') === null){
			return view('frontend.user.login');
		}
		else {
			return redirect(route('front-home'));
		}
	}
	/**
	 * this will check login credentials and if sucess logged into systen
	 * @param  FrontendLoginRequest $request validated credentials
	 * @return void                        redierect to login/home page
	 */
	public function postLogin(FrontendLoginRequest $request)
	{
		$user_name 	= $request->username;
		$password  	= $request->password;
		$user	  	= User::where('username',$user_name)
							->where('password',md5($password))
							->where('role_id',4)
							->first();
		if(isset($user->id)){
			//valid user
			if($user->status == "active"){
				$this->setUserDetailsIntoSession($user->id);
				$this->log('Logged In',$user->id);
				$url = Session::get('back_url') !== null ? Session::get('back_url') : route('front-home');
				return redirect($url);
			}else{
				$this->setFlashAlert('danger',__('login.Please active your account first'));
			}
		}else{
			//invalid credential
			$this->setFlashAlert('danger',__('login.Wrong Credentials'));
		}
		return back();
	}
	/**
	 * this will flush session and logged out
	 * @return void           redirect to login page
	 */
	public function postLogOut(Request $request)
	{
		$user_id = Session::get('user_details')['user_id'];
		$this->log('Logged Out',$user_id);
		$request->session()->flush();
		$this->setFlashAlert('danger',__('login.Successfully Logged out'));
		return redirect(route('front-home'));
	}
	/**
	 * this will send a forgot password link to user to reset passowrd
	 * @param  Request $request request object
	 * @return bool           true for success and false invalid email
	 */
	public function sendForgotPasswordLink(Request $request)
	{
		$email = $request->email;
		$user = User::where('email',$email)->where('role_id',4)->first();
		if(isset($user->id)){
			$unique_code = str_random(50);
			$user = User::find($user->id);
			$user->unique_code 	= $unique_code;
			$user->save();
			Mail::to($email)->queue(new FrontendForgetPasswordMail($user));
			$this->log('reset password link sent',$user->id);
			echo 1;
		}else{
			echo 0;
		}
	}

	/**
	 *  this will load reset password page
	 *
	 *  @param   string  $token  token sent to mail
	 *  @return	 void|string load page if valid else redirect to login page
	 */
	public function getResetPassword($token)
	{
		$user = User::where('unique_code',$token)->where('role_id',4)->first();
		if(isset($user->id)){
			return view('frontend.user.reset_password');
		}else{
			$this->setFlashAlert('danger',__('registration.Invalid Link'));
			return redirect(route('front-get-login'));
		}
	}

	/**
	 *  this will reset password for not logged in user
	 *
	 *  @param   FrontendChangePasswordRequest  $request  validated request
	 *  @return  void 									  redirect to login page
	 */
	public function postResetPassword(FrontendChangePasswordRequest $request)
	{
		$url = url()->previous();
		$token = str_after($url, 'reset-password/');
		$user = User::where('unique_code',$token)->where('role_id',4)->first();
		if(isset($user->id)){
			User::where('id',$user->id)->update(['password' => md5($request->password),'status' => 'active']);
			$this->log('password changed',$user->id);
			$this->setFlashAlert('success',__('registration.Password Updated'));
		}else{
			$this->setFlashAlert('danger',__('registration.Invalid Link'));
		}
		return redirect(route('front-get-login'));
	}
}
