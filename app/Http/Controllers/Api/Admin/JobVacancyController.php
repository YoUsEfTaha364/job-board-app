<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateJobVacancyRequest;
use App\Http\Requests\FilterJobVacancyRequest;
use App\Http\Requests\UpdateJobVacancyRequest;
use App\Http\Resources\JobVacancyResource;
use App\Models\JobVacancy;
use App\Services\ApiResponseService;
use App\Services\FilterJobVacanciesService;
use App\Services\JobVacancyService;
use Illuminate\Http\Request;

class JobVacancyController extends Controller
{
    protected JobVacancyService $jobService;
    protected FilterJobVacanciesService $filterService;

    public function __construct(JobVacancyService $jobService,FilterJobVacanciesService $ser)
    {
        $this->jobService = $jobService;
        $this->filterService = $ser;
    }

    public function index(FilterJobVacancyRequest $request)
    {
        $validated = $request->validated();

        $jobs = $this->filterService->filterAdminVacancies($validated);

        if ($jobs->isEmpty()) {
            return ApiResponseService::Response(200, "no jobs found", []);
        }

        $response = [
            "jobs" => JobVacancyResource::collection($jobs),
            "pagination" => [
                "current_page" => $jobs->currentPage(),
                "last_page" => $jobs->lastPage(),
                "per_page" => $jobs->perPage(),
                "total" => $jobs->total(),
                "next_page_url" => $jobs->nextPageUrl(),
                "prev_page_url" => $jobs->previousPageUrl()
            ]
        ];

        return ApiResponseService::Response(
            200,
            "get jobs",
            $response
        );
    }

    public function getArchivedJobs()
    {
        $jobs = $this->jobService->getArchivedJobs(10);

        if ($jobs->isEmpty()) {
            return ApiResponseService::Response(200, "no archived jobs found", []);
        }

        $response = [
            "jobs" => JobVacancyResource::collection($jobs),
            "pagination" => [
                "current_page" => $jobs->currentPage(),
                "last_page" => $jobs->lastPage(),
                "per_page" => $jobs->perPage(),
                "total" => $jobs->total(),
                "next_page_url" => $jobs->nextPageUrl(),
                "prev_page_url" => $jobs->previousPageUrl()
            ]
        ];

        return ApiResponseService::Response(200, "get archived jobs", $response);
    }

    public function show(JobVacancy $jobVacancy)
    {
        $job = $this->jobService->getJob($jobVacancy);

        $response = [
            "job" => new JobVacancyResource($job)
        ];

        return ApiResponseService::Response(200, "get job", $response);
    }

    public function store(CreateJobVacancyRequest $request)
    {
        $validated = $request->validated();

        $job = $this->jobService->createJob($validated);

        $response = [
            "job" => new JobVacancyResource($job)
        ];

        return ApiResponseService::Response(201, "job created successfully", $response);
    }

    public function update(UpdateJobVacancyRequest $request, JobVacancy $jobVacancy)
    {
        $validated = $request->validated();

        $job = $this->jobService->updateJob($jobVacancy, $validated);

        $response = [
            "job" => new JobVacancyResource($job)
        ];

        return ApiResponseService::Response(200, "job updated successfully", $response);
    }

    public function destroy(JobVacancy $jobVacancy)
    {
        $this->jobService->permanentDelete($jobVacancy);

        return ApiResponseService::Response(200, "job deleted successfully", []);
    }

    public function archive(JobVacancy $jobVacancy)
    {
        $this->jobService->archiveJob($jobVacancy);

        return ApiResponseService::Response(200, "job archived successfully", []);
    }

    public function restore($id)
    {
        $jobVacancy = JobVacancy::onlyTrashed()->findOrFail($id);
        $this->jobService->restoreJob($jobVacancy);

        $response = [
            "job" => new JobVacancyResource($jobVacancy)
        ];

        return ApiResponseService::Response(200, "job restored successfully", $response);
    }
}
