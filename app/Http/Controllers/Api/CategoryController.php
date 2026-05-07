<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\JobCategory;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = JobCategory::all();

        if ($categories->count() <= 0) {

            return ApiResponseService::Response(200, "no categories found", []);
        }

        $response = [
            "categories" => CategoryResource::collection($categories)
        ];

        return ApiResponseService::Response(200, "get categories", $response);
    }

    public function getArchived() {
        

        $categories=JobCategory::onlyTrashed()->get();

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


        $response = [
            "category" => new CategoryResource($category)
        ];


        return ApiResponseService::Response(201, "category created successfully", $response);
    }
    public function  update(UpdateCategoryRequest $request,JobCategory $jobcategory)
    {
        $validated = $request->validated();
        $jobcategory->update([
            "name" => $validated["name"]
        ]);

        $response = [
            "category" => new CategoryResource($jobcategory)
        ];

        return ApiResponseService::Response(200, "category updated successfully", $response);
    }

    public function show(JobCategory $jobcategory)  {

        $response = [
            "category" => new CategoryResource($jobcategory)
        ];

        return ApiResponseService::Response(200, "get category", $response);
        
    }
    public function archive(JobCategory $jobcategory)  {

        $jobcategory->delete(); 

        return ApiResponseService::Response(200, "category archived", []);
        
    }
    public function restore(JobCategory $jobcategory)  {
    
        $jobcategory->restore(); 

         $response = [
            "category" => new CategoryResource($jobcategory)
        ];

        return ApiResponseService::Response(200, "category restored successfully", $response);
        
    }
}
