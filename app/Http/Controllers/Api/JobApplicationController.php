<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Services\ApiResponseService;
use App\Services\JobApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{

    protected JobApplicationService $jobService;
    public function __construct(JobApplicationService $job){
        $this->jobService=$job;
       
    }


    public function index()  {
        $applications=JobApplication::with("jobVacancy")->where("user_id",Auth::user()->id)->get();

         if ($applications->isEmpty()) {
            return ApiResponseService::Response(200, "no applications found", []);
        }

         $response = [
            "applications" => JobApplicationResource::collection($applications)
        ];



        return ApiResponseService::Response(
            201, 
            "get user job apps", 
            $response   
        );

    }
    
    public function store(CreateJobApplicationRequest $request)  {
        $validated=$request->validated();
       
        $application = $this->jobService->storeJob($validated);
        
        return ApiResponseService::Response(
            201, 
            "Job application created and analyzed successfully", 
            new JobApplicationResource($application)
        );
    }

    public function show(JobApplication $jobapplication)
    {
        return ApiResponseService::Response(200, "Job application retrieved successfully", new JobApplicationResource($jobapplication));
    }

    public function archive(JobApplication $jobapplication)
    {
        $jobapplication->delete();
        return ApiResponseService::Response(200, "Job application archived successfully", []);
    }

    public function delete(JobApplication $jobapplication)
    {
        $jobapplication->forceDelete();
        return ApiResponseService::Response(200, "Job application deleted permanently", []);
    }

    public function restore($id)
    {
        $jobApplication = JobApplication::withTrashed()->findOrFail($id);
        $jobApplication->restore();
        return ApiResponseService::Response(200, "Job application restored successfully", new JobApplicationResource($jobApplication));
    }

    public function getArchived()
    {
        $applications = JobApplication::with("jobVacancy")->where("user_id", Auth::user()->id)->onlyTrashed()->get();
        return ApiResponseService::Response(200, "Archived job applications retrieved", JobApplicationResource::collection($applications));
    }
}
