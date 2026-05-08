<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\JobVacancyController;
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
  Route::middleware(["auth:api","admin"])->controller(CompanyController::class)->group(function(){
  
    // get all companies without archived
     Route::get("companies","index");
     // get archived categories
     Route::get("companies/archived","getArchived"); //done
     Route::get("companies/{company}","show"); //done

     Route::post("companies/store","store");
     Route::put("companies/{company}","update");

     Route::delete("companies/{company}","archive");

     Route::put("companies/{company}/restore","restore")->withTrashed();

     



     
     
  });

  Route::middleware(["auth:api","admin"])->controller(JobVacancyController::class)->group(function(){
  
     Route::get("job-vacancies","index");//done
     Route::get("job-vacancies/archived","getArchivedJobs");//done
     Route::get("job-vacancies/{jobVacancy}","show");//done
     Route::post("job-vacancies/store","store");//done
     Route::put("job-vacancies/{jobVacancy}","update");//done
     Route::delete("job-vacancies/{jobVacancy}","destroy");
     Route::put("job-vacancies/{jobVacancy}/archive","archive");//done
     Route::put("job-vacancies/{jobVacancy}/restore","restore")->withTrashed();//done
     
  });


