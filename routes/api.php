<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CompanyController;
use App\Http\Controllers\Api\Admin\JobApplicationController as AdminJobApplicationController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\Admin\JobVacancyController;
use App\Http\Controllers\Api\Company\JobApplicationController as CompanyJobApplicationController;
use App\Http\Controllers\Api\Company\JobVacancyController as CompanyJobVacancyController;
use App\Http\Controllers\Api\ResumeController;
use App\Http\Controllers\S3TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');




Route::controller(AuthController::class)->group(function () {

   Route::post("login", "login");
   Route::post("register", "register");
   Route::middleware("auth:api")->get("user", "user");
   Route::middleware("auth:api")->delete("logout", "logout");
});

// admin

Route::prefix("admin/")->middleware(["auth:api", "admin"])->controller(CategoryController::class)->group(function () {

   // get all categories without archived
   Route::get("categories", "index");
   // get archived categories
   Route::get("categories/archived", "getArchived");

   Route::post("categories/store", "store");

   Route::get("categories/{jobcategory}", "show");

   Route::put("categories/{jobcategory}", "update");

   Route::put("categories/{jobcategory}/restore", "restore")->withTrashed();

   Route::delete("categories/{jobcategory}", "archive");
});
Route::prefix("admin/")->middleware(["auth:api", "admin"])->controller(CompanyController::class)->group(function () {

   // get all companies without archived
   Route::get("companies", "index");
   // get archived categories
   Route::get("companies/archived", "getArchived"); //done
   Route::get("companies/{company}", "show"); //done

   Route::post("companies/store", "store");
   Route::put("companies/{company}", "update");

   Route::delete("companies/{company}", "archive");

   Route::put("companies/{company}/restore", "restore")->withTrashed();
});

Route::prefix("admin/")->middleware(["auth:api", "admin"])->controller(JobVacancyController::class)->group(function () {

   Route::get("job-vacancies", "index"); //done
   Route::get("job-vacancies/archived", "getArchivedJobs"); //done
   Route::get("job-vacancies/{jobVacancy}", "show"); //done
   Route::post("job-vacancies/store", "store"); //done
   Route::put("job-vacancies/{jobVacancy}", "update"); //done
   Route::delete("job-vacancies/{jobVacancy}", "destroy");
   Route::put("job-vacancies/{jobVacancy}/archive", "archive"); //done
   Route::put("job-vacancies/{jobVacancy}/restore", "restore")->withTrashed(); //done

});

Route::prefix("admin/")->middleware(["auth:api", "admin"])->controller(AdminJobApplicationController::class)->group(function () {
   Route::get("applications", [AdminJobApplicationController::class, "index"]); //done
   Route::get("applications/{jobapplication}", [AdminJobApplicationController::class, "show"]); //done

});

//company
Route::prefix("company/")->middleware(["auth:api", "company"])->controller(CompanyJobVacancyController::class)->group(function () {

   Route::get("job-vacancies", "index"); //done
   Route::get("job-vacancies/archived", "getArchivedJobs");
   Route::get("job-vacancies/{jobVacancy}", "show"); //done
   Route::post("job-vacancies", "store"); //done
   Route::put("job-vacancies/{jobVacancy}", "update"); //done
   Route::delete("job-vacancies/{jobVacancy}", "destroy");
   Route::put("job-vacancies/{jobVacancy}/archive", "archive"); //done
   Route::put("job-vacancies/{jobVacancy}/restore", "restore")->withTrashed(); //done

});

Route::prefix("company/")->middleware(["auth:api", "company"])->controller(CompanyJobApplicationController::class)->group(function () {

   Route::get("applications", [CompanyJobApplicationController::class, "index"]); //done
   Route::get("applications/{jobapplication}", [CompanyJobApplicationController::class, "show"]); //done

});

Route::middleware(["auth:api"])->controller(ResumeController::class)->group(function () {

   Route::get("resumes", [ResumeController::class, "index"]); //done
   Route::get("resumes/archived", [ResumeController::class, "getArchived"]); //done

   Route::get("resumes/{resume}", [ResumeController::class, "show"]); //done
   Route::post("resumes", [ResumeController::class, "store"]); //done


   Route::put("resumes/{resume}/archive", [ResumeController::class, "archive"]); //done
   Route::put("resumes/{resume}/restore", [ResumeController::class, "restore"])->withTrashed(); //done

   Route::delete("resumes/{resume}", [ResumeController::class, "delete"]); //done

   Route::get("resumes/archived", [ResumeController::class, "getArchived"]); //done

});





Route::middleware(["auth:api"])->controller(JobApplicationController::class)->group(function () {


   Route::get("applications", [JobApplicationController::class, "index"]); //done
   Route::get("applications/archived", [JobApplicationController::class, "getArchived"]); //done
   Route::get("applications/{jobapplication}", [JobApplicationController::class, "show"]); //done
   Route::post("applications", [JobApplicationController::class, "store"]); //done

   Route::put("applications/{jobapplication}/archive", [JobApplicationController::class, "archive"]); //done

   Route::put("applications/{jobapplication}/restore", [JobApplicationController::class, "restore"]); //done

   Route::delete("applications/{jobapplication}", [JobApplicationController::class, "delete"]); //done



});
