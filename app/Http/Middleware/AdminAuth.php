<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\Http\Traits\Rbac;

/**
 *  This is a Middleware which will validate all admin request.
 *  It use Rbac trait to access control.
 *
 *  @author Anirban Saha
 */
class AdminAuth
{
    use Rbac;
	
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$slug=null)
    {
        if($this->isLoggedIn()){
            if($slug !=null){
                if(strpos($slug,'@')){
                    $module_slug =  explode('@',$slug)[0];
                    $method_name =  explode('@',$slug)[1];
                }else{
                    $module_slug =  $slug;
                    $method_name =  'canView';
                }
                if($this->{$method_name}($module_slug)){
                    return $next($request);
                }else{
                    Session::flash('alert_class', 'danger');
                    Session::flash('alert_msg', 'access denied');
                    return redirect(route('admin-dashboard'));
                }
            }
            return $next($request);
        }
        else
        {
            Session::flash('alert_class', 'danger');
            Session::flash('alert_msg', 'Log in first');
            return redirect(route('admin-login'));
        };
    }
}
