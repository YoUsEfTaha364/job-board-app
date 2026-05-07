<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Resume;

class JobApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $vacancies = JobVacancy::all();
        $resumes = Resume::all();

        if ($vacancies->isNotEmpty() && $resumes->isNotEmpty()) {
            $vacancy = $vacancies->first();
            $resume = $resumes->first();

            JobApplication::create([
                'status' => 'pending',
                'ai_generated_score' => 85.5,
                'ai_generated_feedback' => 'Good match for the required skills.',
                'job_vacancy_id' => $vacancy->id,
                'resume_id' => $resume->id,
                'user_id' => $resume->user_id,
            ]);
        }
    }
}
