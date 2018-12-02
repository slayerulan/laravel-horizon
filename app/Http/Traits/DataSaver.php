<?php
namespace App\Http\Traits;

use App\User;
use App\UsersActivityLog;
use App\SystemActivityLog;
use Illuminate\Support\Facades\Session;

/**
 * This contain several function that help to store data into session or database
 *
 *  @author Anirban Saha
 */
trait DataSaver
{
	/**
	 * This will log users activity into table
	 *
	 * @param  string $event   what he did
	 * @param  integer $user_id nullable for logged in user
	 */
	public function log(string $event,$user_id = null)
	{
		$data['user_id'] = $user_id == null ? $this->user_id : $user_id;
		$data['event']	 = $event;
		UsersActivityLog::create($data);
	}
	/**
	 * This will log system activity into table
	 *
	 * @param  string $event   what he did
	 * @param  string $details nullable/details of event in json format
	 */
	public function logFeed($insert_array)
	{
		SystemActivityLog::create($insert_array);
	}
	/**
	 * Set user details into session
	 *
	 * @param int $user_id user_id
	 */
	public function setUserDetailsIntoSession($user_id = null)
	{
		$user_id = $user_id !== null ? $user_id : $this->user_id;
		$user = User::find($user_id);
		$image_link = $user->user_profile->profile_image != null ? asset('storage/'.$user->user_profile->profile_image) : asset('frontend/images/user-profile-img.png');
		$user_details = [
			'id' 			=> $user->id,
			'role_id'		=> $user->role_id,
			'username' 		=> $user->username,
			'email' 		=> $user->email,
			'profile_image' => $image_link,
			'bet_rule'		=> $user->getBetRule(),
		];
		Session::put('user_details',$user_details);
	}
    /**
	 * Set user details into session for admin panel
	 *
	 * @param int $user_id user_id
	 */
	public function setUserDetailsIntoSessionAdmin($user_id = null)
	{
		$user_id = $user_id !== null ? $user_id : $this->user_id;
		$user = User::find($user_id);
		$image_link = $user->user_profile->profile_image != null ? asset('storage/'.$user->user_profile->profile_image) : asset('frontend/images/user-profile-img.png');
		$user_details = [
			'id' 			=> $user->id,
			'role_id'		=>	$user->role_id,
			'username' 		=> $user->username,
			'email' 		=> $user->email,
			'profile_image' => $image_link,
		];
		Session::put('admin_details',$user_details);
	}
	/**
	 * set alert message into session
	 *
	 * @param string $class   danger/success
	 * @param string $message translated alert message
	 */
	public function setFlashAlert($class,$message)
	{
		Session::flash('alert_class', $class);
		Session::flash('alert_msg',$message);
	}
}

 ?>
