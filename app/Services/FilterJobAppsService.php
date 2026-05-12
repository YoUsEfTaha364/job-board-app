<?php

namespace App\Services;

use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

class FilterJobAppsService{
    public function filterAdminApps(array $validated)  {
        $query = JobApplication::query()
            ->with('jobVacancy');

        $query->when(
            isset($validated['status']),
            fn($q) => $q->where('status', $validated['status'])
        );

        $query->when(
            isset($validated['job_vacancy_id']),
            fn($q) => $q->where('job_vacancy_id', $validated['job_vacancy_id'])
        );

        $query->when(
            isset($validated['job_vacancy_type']),
            function ($q) use ($validated) {
                $q->whereHas('jobVacancy', function ($query) use ($validated) {
                    $query->where('type', $validated['job_vacancy_type']);
                });
            }
        );

        $query->when(
            isset($validated['search']),
            function ($q) use ($validated) {
                $search = $validated['search'];

                $q->whereHas('jobVacancy', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%");
                    });
                });
            }
        );

        return $query->paginate(10);
        
    }
    public function filterCompanyApps(array $validated)  {
        $query = JobApplication::query()
            ->with('jobVacancy')->join("job_vacancies","job_vacancies.id","job_applications.job_vacancy_id")->where("job_vacancies.company_id",Auth::user()->company->id);

        $query->when(
            isset($validated['status']),
            fn($q) => $q->where('status', $validated['status'])
        );

        $query->when(
            isset($validated['job_vacancy_id']),
            fn($q) => $q->where('job_vacancy_id', $validated['job_vacancy_id'])
        );

        $query->when(
            isset($validated['job_vacancy_type']),
            function ($q) use ($validated) {
                $q->whereHas('jobVacancy', function ($query) use ($validated) {
                    $query->where('type', $validated['job_vacancy_type']);
                });
            }
        );

        $query->when(
            isset($validated['search']),
            function ($q) use ($validated) {
                $search = $validated['search'];

                $q->whereHas('jobVacancy', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%");
                    });
                });
            }
        );

        return $query->paginate(10);
        
    }
}