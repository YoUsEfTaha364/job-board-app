<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
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

  Route::middleware(["auth:api","admin"])->controller(CategoryController::class)->group(function(){
  
    // get all categories without archived
     Route::get("categories","index");
     // get archived categories
     Route::get("categories/archived","getArchived");


     Route::post("categories/store","store");
    
     Route::get("categories/{jobcategory}","show");

      Route::put("categories/{jobcategory}","update");

      Route::put("categories/{jobcategory}/restore","restore")->withTrashed();

     Route::delete("categories/{jobcategory}","archive");


     


     
  });


