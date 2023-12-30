<?php

namespace App\Http\Middleware;
use Closure, Hash, Helper;


class Authlogin {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) 
    {
        $session = session('admin_session');
        if(!isset($session) || empty($session)){
            return redirect('auth/login')->with('warning', 'Mohon login terlebih dahulu');
        }
        // $token = session('login_session');
        // if(!isset($token) || empty($token) || $token != Helper::base64_e($session->U_USERNAME) ) {
        //     return redirect('auth/login')->with('warning', 'autentikasi user tidak valid, silahkan login dahulu');
        // }
        else{
            return $next($request);
        }
       
    }

}