<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterJobApplicationRequest;
use App\Http\Resources\AdminJobApplicationResource;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use App\Services\ApiResponseService;
use App\Services\FilterJobAppsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $applications = $this->appService->filterAdminApps($validated);

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
            "get all job apps",
            $response
        );
    }

    public function getArchived()
    {

        $applications = JobApplication::with('jobVacancy')->withTrashed()->where("admin_archived", true)->paginate(10);

        if ($applications->isEmpty()) {
            return ApiResponseService::Response(200, "no archived applications found", []);
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
            "get all archived job apps",
            $response
        );
    }

    public function show(JobApplication $jobapplication)
    {

        $response = [
            "application" => new AdminJobApplicationResource($jobapplication->load(["jobVacancy", "user", "resume"]))
        ];

        return ApiResponseService::Response(200, "get a gjob", $response);
    }

    public function changeStatus(Request $request, JobApplication $jobapplication)
    {



        $validator =  Validator::make(
            $request->all(),
            [
                "status" => "required|string|in:pending,accepted,rejected"
            ]
        );
        $errors = $validator->errors();

        if ($validator->fails()) {
            return ApiResponseService::Response(400, "attribute errors", $errors);
        }
        $validated = $validator->validated();


        $jobapplication->update([
            "status" => $validated["status"]
        ]);

        $response = [
            "application" => new AdminJobApplicationResource($jobapplication->load(["jobVacancy", "user", "resume"]))
        ];


        return ApiResponseService::Response(200, "status changed successfully", $response);
    }

    public function archive(JobApplication $jobapplication)
    {


        $jobapplication->update([
            "admin_archived" => true
        ]);

        $jobapplication->delete();

        return ApiResponseService::Response(200, "application archived successfully", []);
    }
    public function restore(JobApplication $jobapplication)
    {
        $jobapplication->update([
            "admin_archived" => false
        ]);

        if ($jobapplication->trashed()) {
            $jobapplication->restore();
        }

        $response = [
            "application" => new AdminJobApplicationResource($jobapplication->load(["jobVacancy", "user", "resume"]))
        ];
        return ApiResponseService::Response(200, "restore archived job", $response);
    }

    public function delete(JobApplication $jobapplication)
    {
        $jobapplication->forceDelete();
        return ApiResponseService::Response(200, "job app deleted permanently", []);
    }
}
