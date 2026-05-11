<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResumeRequest;
use App\Http\Resources\ResumeResource;
use App\Models\Resume;
use App\Services\ApiResponseService;
use App\Services\ResumeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResumeController extends Controller
{
    protected ResumeService $resumeService;
    public function __construct(ResumeService $resume){
        $this->resumeService=$resume;
       
    }


   
    public function index()
    {
        $resumes = Resume::where("user_id", Auth::user()->id)->get();
        if ($resumes->isEmpty()) {
            return ApiResponseService::Response(200, "no resumes found", []);
        }

        return ApiResponseService::Response(200, "Resumes retrieved successfully", [
            "resumes" => ResumeResource::collection($resumes),
        ]);
    }



    public function store(CreateResumeRequest $request)
    {
        $validated = $request->validated();

        $file = $validated["file"];
        $resume = $this->resumeService->uploadFile($file);

        return ApiResponseService::Response(201, "Resume processed successfully",new ResumeResource($resume));
    }


    public function show(Resume $resume)
    {
        return ApiResponseService::Response(200, "Resume processed successfully",new ResumeResource($resume));
    }


    public function archive(Resume $resume)
    {
        $resume->delete();
        return ApiResponseService::Response(200, "Resume archived successfully",[]);
    }



    public function delete(Resume $resume)
    {
        $resume->forceDelete();
        return ApiResponseService::Response(200, "Resume deleted permanently",[]);
    }

    public function restore(Resume $resume)
    {
        $resume->restore();
        return ApiResponseService::Response(200, "Resume restored successfully",new ResumeResource($resume));
    }

    public function getArchived()
    {
      $resumes = Resume::where("user_id", Auth::user()->id)->withTrashed()->get();
        return ApiResponseService::Response(200, "get archived resumes", ResumeResource::collection($resumes));
    }
}
