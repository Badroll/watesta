<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as mCtl;
use Illuminate\Http\Request;
use App\Model as M;
use Helper, DB;
use Carbon\Carbon;

class AdministratorController extends mCtl
{

    protected $request;

    public function __construct(Request $req) {
    }

    public function user(Request $req){
        $user = DB::table("_user")->get();
        $refRole = DB::table("_reference")->where("R_CATEGORY", "GROUP_ROLE")->where("R_ID", "!=", "ROLE_SUPERADMIN")->get();
        $refStatus = Helper::getReference("USER_STATUS");
        $data["ctl_data"] = $user;
        $data["ctl_refRole"] = $refRole;
        $data["ctl_refStatus"] = $refStatus;

        return view("main.user-manager", $data);
    }


    public function userSave(Request $req){
        return app('App\Http\Controllers\AuthController')->doRegister($req);
    }


    public function userDetail(Request $req){
        return app('App\Http\Controllers\AuthController')->userInfo($req);
    }


    public function userUpdate(Request $req){
        return app('App\Http\Controllers\AuthController')->doReset($req);
    }


    public function userDelete(Request $req){
        return app('App\Http\Controllers\AuthController')->doDelete($req);
    }

}