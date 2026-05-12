<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterJobApplicationRequest;
use App\Http\Resources\AdminJobApplicationResource;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use App\Services\ApiResponseService;
use App\Services\FilterJobAppsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class JobApplicationController extends Controller
{
    protected FilterJobAppsService $appService;

    public function __construct(FilterJobAppsService $Service)
    {
        $this->appService = $Service;
    }
    public function index(FilterJobApplicationRequest $request)
    {
        
        $validated = $request->validated();
        $applications = $this->appService->filterCompanyApps($validated);

        if ($applications->isEmpty()) {
            return ApiResponseService::Response(200, "no applications found", []);
        }

        $response = [
            "applications" => JobApplicationResource::collection($applications),
            "pagination" => [
                "current_page" => $applications->currentPage(),
                "last_page" => $applications->lastPage(),
                "per_page" => $applications->perPage(),
                "total" => $applications->total(),
                "next_page_url" => $applications->nextPageUrl(),
                "prev_page_url" => $applications->previousPageUrl()
            ]
        ];

        return ApiResponseService::Response(
            200,
            "get company job apps",
            $response
        );
    }

    public function show(JobApplication $jobapplication)
    {
       

        Gate::authorize("show", $jobapplication->jobVacancy);

        $response = [
            "application" => new AdminJobApplicationResource($jobapplication->load(["jobVacancy", "user", "resume"]))
        ];

        return ApiResponseService::Response(200, "get a job", $response);
    }
}
