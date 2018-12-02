<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Traits\Authentication;
use Illuminate\Support\Facades\Session;

/**
 *  This is a Middleware which will validate all frontend request.
 *
 *  @author Anirban Saha
 */

class FrontendAuth
{
	use Authentication;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if($this->isLoggedIn($request)){
            return $next($request);
        }
        else
        {
			Session::flash('alert_class', 'danger');
	        Session::flash('alert_msg', __('login.Login First'));
			return redirect(route('front-get-login'));
        };
    }
}
