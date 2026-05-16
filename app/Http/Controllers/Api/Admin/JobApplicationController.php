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
use Illuminate\Support\Facades\Cache;
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

        //todo caching filters problems
        $params = array_merge($validated, [
            'page' => request('page', 1)
        ]);
        ksort($params);
        $key = md5(json_encode($params));

        $applications = Cache::tags(["applications"])->remember("admin.job_applications.$key", 3600, function () use ($validated) {
            return $this->appService->filterAdminApps($validated);
        });

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
      $application=  Cache::tags(["applications"])->remember("applications_".$jobapplication->id, 3600, function () use ($jobapplication) {
            return JobApplication::with(['jobVacancy', 'user', 'resume'])
                ->findOrFail($jobapplication->id);
        });

        $response = [
            "application" => new AdminJobApplicationResource($application)
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

        Cache::tags(["applications"])->flush();



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

        Cache::tags(["applications"])->flush();

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

         Cache::tags(["applications"])->flush();

        $response = [
            "application" => new AdminJobApplicationResource($jobapplication->load(["jobVacancy", "user", "resume"]))
        ];
        return ApiResponseService::Response(200, "restore archived job", $response);
    }

    public function delete(JobApplication $jobapplication)
    {
         Cache::tags(["applications"])->flush();
        $jobapplication->forceDelete();

        return ApiResponseService::Response(200, "job app deleted permanently", []);
    }
}
