<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\JobCategory;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $categories =Cache::tags(["categories"])->remember("categories", 3600, function () {
            return JobCategory::all();
        });

        
        if ($categories->count() <= 0) {

            return ApiResponseService::Response(200, "no categories found", []);
        }

        $response = [
            "categories" => CategoryResource::collection($categories)
        ];

        return ApiResponseService::Response(200, "get categories", $response);
    }

    public function getArchived()
    {


        $categories = JobCategory::onlyTrashed()->get();

        if ($categories->count() <= 0) {

            return ApiResponseService::Response(200, "no archived categories found", []);
        }

        $response = [
            "archivedCategories" => CategoryResource::collection($categories)
        ];

        return ApiResponseService::Response(200, "get archivedCategories", $response);
    }

    public function  store(CreateCategoryRequest $request)
    {

        $validated = $request->validated();
        $category = JobCategory::create([
            "name" => $validated["name"]
        ]);


        Cache::tags(["categories"])->flush();


        $response = [
            "category" => new CategoryResource($category)
        ];


        return ApiResponseService::Response(201, "category created successfully", $response);
    }
    public function  update(UpdateCategoryRequest $request, JobCategory $jobcategory)
    {
        $validated = $request->validated();
        $jobcategory->update([
            "name" => $validated["name"]
        ]);

        Cache::tags(["categories"])->flush();

        $response = [
            "category" => new CategoryResource($jobcategory)
        ];

        return ApiResponseService::Response(200, "category updated successfully", $response);
    }

    public function show(JobCategory $jobcategory)
    {

        $response = [
            "category" => new CategoryResource($jobcategory)
        ];

        return ApiResponseService::Response(200, "get category", $response);
    }
    public function archive(JobCategory $jobcategory)
    {
        
        $jobcategory->delete();

        Cache::tags(["categories"])->flush();


        return ApiResponseService::Response(200, "category archived", []);
    }
    public function restore(JobCategory $jobcategory)
    {

        $jobcategory->restore();

        Cache::tags(["categories"])->flush();

        $response = [
            "category" => new CategoryResource($jobcategory)
        ];

        return ApiResponseService::Response(200, "category restored successfully", $response);
    }
}
