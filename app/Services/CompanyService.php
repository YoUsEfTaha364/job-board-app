<?php

namespace App\Services;

use App\Models\Company;

class CompanyService
{
    public function getAllCompanies($perPage = 10)
    {
        return Company::withCount("jobs")->paginate($perPage);
    }

    public function getArchivedCompanies($perPage = 10)
    {
        return Company::onlyTrashed()->withCount("jobs")->paginate($perPage);
    }

    public function getCompany(Company $company)
    {
        $company->loadCount('jobs');
        return $company;
    }

    public function createCompany(array $data)
    {
        return Company::create($data);
    }

    public function updateCompany(Company $company, array $data)
    {
        $company->update($data);
        return $company;
    }

    public function archiveCompany(Company $company)
    {
        return $company->delete();
    }

    public function restoreCompany(Company $company)
    {
        return $company->restore();
    }
}
