<?php

namespace App\Http\Middleware;
use Closure, Helper;


class ValidatePeriode {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) 
    {
    	$dateLimit = date("Y-m");
    	$periode = $request->periode;

        if(!Helper::validateDate2($periode, "Y-m")){
            return redirect()->back()->with("warning", "format periode tidak valid ");
        }

        if($periode > $dateLimit){
            return redirect()->back()->with("warning", "Batas maksimal periode adalah ". Helper::bulan($dateLimit));
        }

        return $next($request);
       
    }

}