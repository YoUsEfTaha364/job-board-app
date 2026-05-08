<?php

namespace App\Services;

use App\Models\JobVacancy;

class JobVacancyService
{
    public function getAllJobs($perPage = 10)
    {
        return JobVacancy::with(['company', 'jobCategory'])->paginate($perPage);
    }

    public function getArchivedJobs($perPage = 10)
    {
        return JobVacancy::onlyTrashed()->with(['company', 'jobCategory'])->paginate($perPage);
    }

    public function getJob(JobVacancy $job)
    {
        $job->load(['company', 'jobCategory']);
        return $job;
    }

    public function createJob(array $data)
    {
        return JobVacancy::create($data);
    }

    public function updateJob(JobVacancy $job, array $data)
    {
        $job->update($data);
        return $job;
    }

    public function archiveJob(JobVacancy $job)
    {
        return $job->delete();
    }
    public function permanentDelete(JobVacancy $job)
    {
        return $job->forceDelete();
    }

    public function restoreJob(JobVacancy $job)
    {
        return $job->restore();
    }
}
