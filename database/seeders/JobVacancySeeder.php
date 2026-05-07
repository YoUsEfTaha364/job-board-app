<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobVacancy;
use App\Models\Company;
use App\Models\JobCategory;

class JobVacancySeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $categories = JobCategory::all();

        if ($companies->isNotEmpty() && $categories->isNotEmpty()) {
            JobVacancy::create([
                'title' => 'Senior Backend Developer',
                'description' => 'We are looking for a skilled backend developer proficient in Laravel.',
                'location' => 'New York, NY (Hybrid)',
                'salary' => 120000.00,
                'type' => 'hybrid',
                'company_id' => $companies->first()->id,
                'category_id' => $categories->first()->id,
            ]);

            JobVacancy::create([
                'title' => 'Marketing Specialist',
                'description' => 'Join our dynamic team to lead marketing campaigns.',
                'location' => 'Remote',
                'salary' => 60000.00,
                'type' => 'remote',
                'company_id' => $companies->last()->id,
                'category_id' => $categories->last()->id,
            ]);
        }
    }
}
