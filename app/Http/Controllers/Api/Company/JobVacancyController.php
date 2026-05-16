<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyJobRequest;
use App\Http\Requests\CreateJobVacancyRequest;
use App\Http\Requests\FilterJobVacancyRequest;
use App\Http\Requests\UpdateCompanyJobRequest;
use App\Http\Requests\UpdateJobVacancyRequest;
use App\Http\Resources\JobVacancyResource;
use App\Models\JobVacancy;
use App\Services\ApiResponseService;
use App\Services\FilterJobVacanciesService;
use App\Services\JobVacancyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class JobVacancyController extends Controller
{
    protected FilterJobVacanciesService $filterService;

    public function __construct(FilterJobVacanciesService $ser)
    {
        $this->filterService = $ser;
    }


    public function index(FilterJobVacancyRequest $request)
    {

        $validated = $request->validated();


        $params = array_merge($validated, [
            "page" => request("page", 1),
            'company_id' => auth::user()->company->id
        ]);

        ksort($params);

        $key = "company_job_vacancies." . md5(json_encode($params));

        $jobs = Cache::tags(["job_vacancies"])->remember($key, 3600, function () use ($validated) {
            return $this->filterService->filterCompanyVacancies($validated);
        });


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

        $job = Cache::tags(["job_vacancies"])->remember("job_vacancies.$jobVacancy->id", 3600, function () use ($jobVacancy) {
            return  $jobVacancy->load(['company', 'jobCategory']);
        });


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

        Cache::tags(["job_vacancies"])->flush();


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

        Cache::tags(["job_vacancies"])->flush();


        $response = [
            "job" => new JobVacancyResource($jobVacancy)
        ];

        return ApiResponseService::Response(200, "job updated successfully", $response);
    }

    public function destroy(JobVacancy $jobVacancy)
    {
        Gate::authorize("show", $jobVacancy);

        $jobVacancy->forceDelete();

        Cache::tags(["job_vacancies"])->flush();


        return ApiResponseService::Response(200, "job deleted successfully", []);
    }

    public function archive(JobVacancy $jobVacancy)
    {
        Gate::authorize("show", $jobVacancy);
        $jobVacancy->delete();

        Cache::tags(["job_vacancies"])->flush();


        return ApiResponseService::Response(200, "job archived successfully", []);
    }

    public function restore(JobVacancy $jobVacancy)
    {

        Gate::authorize("restore", $jobVacancy);
        $jobVacancy->restore();

        Cache::tags(["job_vacancies"])->flush();

        $response = [
            "job" => new JobVacancyResource($jobVacancy)
        ];

        return ApiResponseService::Response(200, "job restored successfully", $response);
    }
}
