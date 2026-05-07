<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


  Route::controller(AuthController::class)->group(function(){

     Route::post("login","login");
     Route::post("register","register");
     Route::middleware("auth:api")->get("user","user");
     Route::middleware("auth:api")->delete("logout","logout");

  });
