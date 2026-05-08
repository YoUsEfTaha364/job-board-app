<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use App\Services\ApiResponseService;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
       
        $companies = $this->companyService->getAllCompanies(10);
        
        if ($companies->isEmpty()) {
            return ApiResponseService::Response(200, "no companies found", []);
        }

        $response = [
            "companies" => CompanyResource::collection($companies),
            "pagination" => [
                "current_page" => $companies->currentPage(),
                "last_page" => $companies->lastPage(),
                "per_page" => $companies->perPage(),
                "total" => $companies->total(),
                "next_page_url" => $companies->nextPageUrl(),
                "prev_page_url" => $companies->previousPageUrl()
            ]
        ];

        return ApiResponseService::Response(200, "get companies", $response);
    }

    public function getArchived()
    {
        $companies = $this->companyService->getArchivedCompanies(10);

        if ($companies->isEmpty()) {
            return ApiResponseService::Response(200, "no archived companies found", []);
        }

        $response = [
            "companies" => CompanyResource::collection($companies),
            "pagination" => [
                "current_page" => $companies->currentPage(),
                "last_page" => $companies->lastPage(),
                "per_page" => $companies->perPage(),
                "total" => $companies->total(),
                "next_page_url" => $companies->nextPageUrl(),
                "prev_page_url" => $companies->previousPageUrl()
            ]
        ];

        return ApiResponseService::Response(200, "get archived companies", $response);
    }

    public function show(Company $company)
    {
        $company = $this->companyService->getCompany($company);

        $response = [
            "company" => new CompanyResource($company)
        ];

        return ApiResponseService::Response(200, "get company", $response);
    }

    public function store(CreateCompanyRequest $request)
    {
        $validated = $request->validated();
        
        $company = $this->companyService->createCompany($validated);

        $response = [
            "company" => new CompanyResource($company)
        ];

        return ApiResponseService::Response(201, "company created successfully", $response);
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $validated = $request->validated();
        
        $company = $this->companyService->updateCompany($company, $validated);

        $response = [
            "company" => new CompanyResource($company)
        ];

        return ApiResponseService::Response(200, "company updated successfully", $response);
    }

    public function archive(Company $company)
    {
        $this->companyService->archiveCompany($company);

        return ApiResponseService::Response(200, "company archived successfully", []);
    }

    public function restore(Company $company)
    {
       
        $this->companyService->restoreCompany($company);

        $response = [
            "company" => new CompanyResource($company)
        ];

        return ApiResponseService::Response(200, "company restored successfully", $response);
    }
}
