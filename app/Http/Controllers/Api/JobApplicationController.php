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
use Illuminate\Support\Facades\Gate;

class JobApplicationController extends Controller
{

    protected JobApplicationService $jobService;
    public function __construct(JobApplicationService $job)
    {
        $this->jobService = $job;
    }


    public function index()
    {
        $applications = JobApplication::with("jobVacancy")->where("user_id", Auth::user()->id)->paginate(10);

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
            201,
            "get user job apps",
            $response
        );
    }

    public function store(CreateJobApplicationRequest $request)
    {
        $validated = $request->validated();

        $application = $this->jobService->storeJob($validated);

        return ApiResponseService::Response(
            201,
            "Job application created and analyzed successfully",
            new JobApplicationResource($application)
        );
    }

    public function show(JobApplication $jobapplication)
    {
        Gate::authorize("seekerActions", $jobapplication);
        return ApiResponseService::Response(200, "Job application retrieved successfully", new JobApplicationResource($jobapplication));
    }

    public function archive(JobApplication $jobapplication)
    {
        Gate::authorize("seekerActions", $jobapplication);
        $jobapplication->update([
            "user_archived" => true
        ]);
        $jobapplication->delete();
        return ApiResponseService::Response(200, "Job application archived successfully", []);
    }

    public function restore(JobApplication $jobapplication)
    {
        Gate::authorize("seekerRestore", $jobapplication);

        if ($jobapplication->trashed()) {
            $jobapplication->restore();
        }

        $jobapplication->update([
            "user_archived" => false
        ]);
        $jobapplication->restore();
        return ApiResponseService::Response(200, "Job application restored successfully", new JobApplicationResource($jobapplication));
    }

    public function getArchived()
    {
        $applications = JobApplication::with("jobVacancy")->where("user_id", Auth::user()->id)->withTrashed()->where("user_archived", true)->get();
        return ApiResponseService::Response(200, "Archived job applications retrieved", JobApplicationResource::collection($applications));
    }
}
