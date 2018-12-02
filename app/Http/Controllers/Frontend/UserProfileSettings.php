<?php
namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Frontend\FrontendBaseController;
use App\Http\Requests\FrontendUpdateProfileRequest;
use App\Http\Requests\FrontendChangePasswordRequest;
use App\Language;
use App\Currency;
use App\Country;
use App\Transaction;

/**
 *  this is the controller from where user profile settings will be managed,
 *  like edit profile, change password etc
 *
 *  @author Anirban Saha
 */
class UserProfileSettings extends FrontendBaseController
{
	/**
	 * Load profile edit form
	 * @return html load edit form
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
		$data['user_details'] = $this->user;
	   return view('frontend.user.profile',$data);
	}
	/**
	 * Update users profile and update session
	 * @param  FrontendUpdateProfileRequest $request validated form request data
	 * @return void                                redirect page
	 */
	public function postUpdateProfile(FrontendUpdateProfileRequest $request)
	{
		$user = $this->user;
		$user->full_name 	= $request->full_name;
		$user->email 		= $request->email;
		$user->mobile 		= $request->mobile;
		$user->save();
		$updated_data 		= [];
		if($request->file('profile_image')) {
            $profile_image 					= $request->file('profile_image')->store('user_image');
			$updated_data['profile_image']	= $profile_image;
			Storage::delete($user->user_profile->profile_image);
		}
		$updated_data['country_id']		= $request->country;
		$updated_data['address']		= $request->address;
		$updated_data['sex']			= $request->gender;
		$updated_data['language_id']	= $request->language;
		$updated_data['currency_id']	= $request->currency;
		$this->user->user_profile->update($updated_data);
		$this->setUserDetailsIntoSession();
		$this->log('Profile Updated');
		$this->setFlashAlert('success',__('registration.Profile Updated'));
		return back();
	}

	/**
	 *  this will load change password page
	 *
	 *  @return  string  load html page
	 */
	public function getChangePassword()
	{
		return view('frontend.user.change_password');
	}

	/**
	 *  this will change password of current user
	 *
	 *  @param   FrontendChangePasswordRequest  $request  validated request data
	 *  @return  void                         redirect back
	 */
	public function postChangePassword(FrontendChangePasswordRequest $request)
	{
		$user_details = $this->user;
		if(md5($request->old_password) == $user_details->password){
			$user_details->password = md5($request->password);
			$user_details->save();
			$this->log('Password Changed');
			$this->setFlashAlert('success',__('registration.Password Updated'));
		}else{
			$this->setFlashAlert('danger',__('registration.Wrong Old Password'));
		}
		return back();
	}

	/**
	 *  this will return users current wallet Balance
	 *
	 *  @return  int  wallet balance
	 */
	public function getWalletBalance()
	{
		return Session::get('user_details')['balance'];
		// return $this->user->getWallet()->amount;
	}

	/**
	 * will show all the transaction history
	 * @return html     transaction history page
	 */
	public function getTransactionHistory() {
		// $wallet = $this->user->getWallet();
		$data['transactions'] = Transaction::where('user_id', $this->user_id)->orderBy('created_at', 'desc')->paginate(TRANSACTION_PAGE_LIMIT);
		$data['page'] = $data['transactions'];
		return view('frontend.user.transactions', $data);
	}

	/**
	 * will get transaction history according to searched value
	 * @param  Request     $request     searched keyword
	 * @return html           transaction history for the search
	 */
	public function postSearchTransactionHistory(Request $request) {
		$key = $request->key;
		// $wallet = $this->user->getWallet();
		$data['transactions'] = Transaction::where('user_id', $this->user_id)->Where('title', 'LIKE', '%'.$key.'%')->orWhere('type', 'LIKE', '%'.$key.'%')->orderBy('created_at', 'desc')->offset(0)->limit(TRANSACTION_PAGE_LIMIT)->get();
		$total_result = Transaction::where('user_id', $this->user_id)->Where('title', 'LIKE', '%'.$key.'%')->orWhere('type', 'LIKE', '%'.$key.'%')->count();
		$data['total_page'] = ceil($total_result / TRANSACTION_PAGE_LIMIT);
		$data['page_no'] = 1;
		return view('frontend.user.transaction_search', $data);
	}

	/**
	 * will get transaction history according to searched value and peginate
	 * @param  Request     $request     searched keyword and page no
	 * @return html           transaction history for the search
	 */
	public function postSearchPaginateTransactionHistory(Request $request) {
		$key = $request->key;
		$page = $request->page;
		$offset = ($page-1)*TRANSACTION_PAGE_LIMIT;
		// $wallet = $this->user->getWallet();
		$data['transactions'] = Transaction::where('user_id', $this->user_id)->Where('title', 'LIKE', '%'.$key.'%')->orWhere('type', 'LIKE', '%'.$key.'%')->orderBy('created_at', 'desc')->offset($offset)->limit(TRANSACTION_PAGE_LIMIT)->get();
		$total_result = Transaction::where('user_id', $this->user_id)->Where('title', 'LIKE', '%'.$key.'%')->orWhere('type', 'LIKE', '%'.$key.'%')->count();
		$data['total_page'] = ceil($total_result / TRANSACTION_PAGE_LIMIT);
		$data['page_no'] = $page;
		return view('frontend.user.transaction_search', $data);
	}
}
