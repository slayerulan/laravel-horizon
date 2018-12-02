<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class LangMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		$default_language = Session::has('selected_lang') ? Session::get('selected_lang') : config('app.locale');
	    \App::setLocale($default_language);
        return $next($request);
    }
}
