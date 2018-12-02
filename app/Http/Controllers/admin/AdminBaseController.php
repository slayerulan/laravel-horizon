<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Rbac;

/**
 *  This is base controller for admin, every controller should inherit this,
 *  so, we can implement any feature to all controller easily. It use Rbac trait for access control.
 *
 *  @author Anirban Saha
 */
class AdminBaseController extends Controller
{
	use Rbac;

	/**
	 *  this will assign user role and user in class property
	 *
	 */
	public function __construct()
	{
		$this->isLoggedIn();
	}
}
