<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get("/", function(){
    $x = "Y";
    $tes = "0";
    if(!isset($tes)){
        $x = "N";
    }
	return "welcome" . $x;
});
Route::post('/', function(){
	return "welcome post";
});
Route::get("tojson", "ApiController@toJson");
Route::get("export", "ApiController@export");

Route::post("hooks", "ApiController@hooks");
Route::match(["get", "post"], "send",
"ApiController@multipleSendtext");
Route::match(["get", "post"], "bc",
"ApiController@broadcast");

Route::group([ "prefix" => "auth" ], function(){
    Route::post('login', "ApiController@login");
    Route::post('register', "ApiController@register");
    Route::post('update', "ApiController@updateProfil");
});

Route::group([ "prefix" => "artikel" ], function(){
    Route::get('/', "ApiController@artikel");
});

Route::group([ "prefix" => "cek" ], function(){
    Route::get('history', "ApiController@cekHistory");
    Route::post('save', "ApiController@cekSave");
});


//
Route::group([ "prefix" => "file" ], function(){
    Route::get('read', "ApiController@fileRead");
    Route::get("download", "ApiController@fileDownload");
});
