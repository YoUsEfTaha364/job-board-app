<?php

namespace App\Services;

use App\Models\JobVacancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FilterJobVacanciesService
{
    public function filterAdminVacancies(array $validated)
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $validated);

        $query->when(
            isset($validated['company_id']),
            fn($q) => $q->where('company_id', $validated['company_id'])
        );

        return $query->paginate(10);
    }

    public function filterCompanyVacancies(array $validated)
    {
        $companyId = Auth::user()->company->id;

        $query = $this->baseQuery()
            ->where('company_id', $companyId);

        $this->applyFilters($query, $validated);

        return $query->paginate(10);
    }

    private function baseQuery(): Builder
    {
        return JobVacancy::query()
            ->with(['company', 'jobCategory']);
    }

    private function applyFilters(Builder $query, array $validated): void
    {
        $query->when(
            isset($validated['status']),
            fn($q) => $q->where('status', $validated['status'])
        );

        $query->when(
            isset($validated['type']),
            fn($q) => $q->where('type', $validated['type'])
        );

        $query->when(
            isset($validated['category_id']),
            fn($q) => $q->where('category_id', $validated['category_id'])
        );

        $query->when(
            isset($validated['min_salary']),
            fn($q) => $q->where('salary', '>=', $validated['min_salary'])
        );

        $query->when(
            isset($validated['max_salary']),
            fn($q) => $q->where('salary', '<=', $validated['max_salary'])
        );

        $query->when(
            isset($validated['start_date']),
            fn($q) => $q->whereDate('created_at', '>=', $validated['start_date'])
        );

        $query->when(
            isset($validated['end_date']),
            fn($q) => $q->whereDate('created_at', '<=', $validated['end_date'])
        );

        $query->when(
            isset($validated['search']),
            function ($q) use ($validated) {
                $search = $validated['search'];

                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            }
        );
    }
}