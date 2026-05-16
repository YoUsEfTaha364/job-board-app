<?php

namespace App\Http\Controllers\Api\Admin;

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
use Illuminate\Support\Facades\Cache;

class CompanyController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

  public function index()
{
    $page = request('page', 1);
    $key = "companies.page_{$page}";

    $companies = Cache::tags(['companies'])->remember($key, 3600, function () {
        return $this->companyService->getAllCompanies(10);
    });

    if ($companies->isEmpty()) {
        return ApiResponseService::Response(200, "no companies found", []);
    }

    return ApiResponseService::Response(200, "get companies", [
        "companies" => CompanyResource::collection($companies),
        "pagination" => [
            "current_page" => $companies->currentPage(),
            "last_page" => $companies->lastPage(),
            "per_page" => $companies->perPage(),
            "total" => $companies->total(),
            "next_page_url" => $companies->nextPageUrl(),
            "prev_page_url" => $companies->previousPageUrl()
        ]
    ]);
}
 public function getArchived()
{
    $key = "companies.archived.page_" . request('page', 1);

    $companies = Cache::tags(['companies'])->remember($key, 3600, function () {
        return $this->companyService->getArchivedCompanies(10);
    });

    if ($companies->isEmpty()) {
        return ApiResponseService::Response(200, "no archived companies found", []);
    }

    return ApiResponseService::Response(200, "get archived companies", [
        "companies" => CompanyResource::collection($companies),
        "pagination" => [
            "current_page" => $companies->currentPage(),
            "last_page" => $companies->lastPage(),
            "per_page" => $companies->perPage(),
            "total" => $companies->total(),
            "next_page_url" => $companies->nextPageUrl(),
            "prev_page_url" => $companies->previousPageUrl()
        ]
    ]);
}

 public function show(Company $company)
{
    $company = Cache::tags(['companies'])->remember(
        "company_{$company->id}",
        3600,
        function () use ($company) {
            return $this->companyService->getCompany($company);
        }
    );

    return ApiResponseService::Response(200, "get company", [
        "company" => new CompanyResource($company)
    ]);
}
    public function store(CreateCompanyRequest $request)
    {
        $validated = $request->validated();
        
        $company = $this->companyService->createCompany($validated);

        Cache::tags(['companies'])->flush();

        $response = [
            "company" => new CompanyResource($company)
        ];

        return ApiResponseService::Response(201, "company created successfully", $response);
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $validated = $request->validated();
        
        $company = $this->companyService->updateCompany($company, $validated);

        Cache::tags(['companies'])->flush();

        $response = [
            "company" => new CompanyResource($company)
        ];

        return ApiResponseService::Response(200, "company updated successfully", $response);
    }

    public function archive(Company $company)
    {
        $this->companyService->archiveCompany($company);

        Cache::tags(['companies'])->flush();

        return ApiResponseService::Response(200, "company archived successfully", []);
    }

    public function restore(Company $company)
    {
       
        $this->companyService->restoreCompany($company);

        Cache::tags(['companies'])->flush();

        $response = [
            "company" => new CompanyResource($company)
        ];

        return ApiResponseService::Response(200, "company restored successfully", $response);
    }
}
