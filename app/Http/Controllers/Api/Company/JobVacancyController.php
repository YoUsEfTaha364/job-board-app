<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyJobRequest;
use App\Http\Requests\CreateJobVacancyRequest;
use App\Http\Requests\UpdateCompanyJobRequest;
use App\Http\Requests\UpdateJobVacancyRequest;
use App\Http\Resources\JobVacancyResource;
use App\Models\JobVacancy;
use App\Services\ApiResponseService;
use App\Services\JobVacancyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class JobVacancyController extends Controller
{


    public function index()
    {

        $company = Auth::user()->company;
        $jobs = JobVacancy::with(['company', 'jobCategory'])->where("company_id", $company->id)->paginate(10);


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

        return ApiResponseService::Response(200, "get jobs", $response);
    }

    public function getArchivedJobs()
    {

        $jobs = JobVacancy::onlyTrashed()->with(['company', 'jobCategory'])->where("company_id", Auth::user()->company->id)->paginate(10);


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
        Gate::authorize("show", $jobVacancy);

        $job = $jobVacancy->load(['company', 'jobCategory']);

        $response = [
            "job" => new JobVacancyResource($job)
        ];

        return ApiResponseService::Response(200, "get job", $response);
    }

    public function store(CreateCompanyJobRequest $request)
    {
        $validated = $request->validated();

        $job = JobVacancy::create([
            'title' => $validated["title"],
            'description' => $validated["description"],
            'location' => $validated["location"],
            'salary' => $validated["salary"],
            'type' => $validated["type"],
            'company_id' => Auth::user()->company->id,
            'category_id' => $validated["category_id"],

        ]);

        $response = [
            "job" => new JobVacancyResource($job)
        ];

        return ApiResponseService::Response(201, "job created successfully", $response);
    }

    public function update(UpdateCompanyJobRequest $request, JobVacancy $jobVacancy)
    {
        Gate::authorize("show", $jobVacancy);
        $validated = $request->validated();

        $jobVacancy->update($validated);

        $response = [
            "job" => new JobVacancyResource($jobVacancy)
        ];

        return ApiResponseService::Response(200, "job updated successfully", $response);
    }

    public function destroy(JobVacancy $jobVacancy)
    {
        Gate::authorize("show", $jobVacancy);

        $jobVacancy->forceDelete();

        return ApiResponseService::Response(200, "job deleted successfully", []);
    }

    public function archive(JobVacancy $jobVacancy)
    {
        Gate::authorize("show", $jobVacancy);
        $jobVacancy->delete();

        return ApiResponseService::Response(200, "job archived successfully", []);
    }

    public function restore(JobVacancy $jobVacancy)
    {

        Gate::authorize("restore", $jobVacancy);
        $jobVacancy->restore();


        $response = [
            "job" => new JobVacancyResource($jobVacancy)
        ];

        return ApiResponseService::Response(200, "job restored successfully", $response);
    }
}
