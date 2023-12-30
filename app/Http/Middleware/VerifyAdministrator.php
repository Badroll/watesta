<?php

namespace App\Http\Middleware;
use Closure, Helper, Session;


class VerifyAdministrator {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) 
    {
    	$role = Session("admin_session")->U_ROLE;
        $method = $request->method();
        if($role == "ROLE_USER"){
            if($method == "GET"){
                return redirect()->back()->with("warning", "Anda tidak memiliki hak akses ke menu ini");
            }else if($method == "POST"){
                return Helper::composeReply2("ERROR", "Access denied");
            }
        }
        return $next($request);
    }

}