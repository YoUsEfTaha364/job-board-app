<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobCategory;

class JobCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Software Development', 'Marketing', 'Finance', 'Human Resources', 'Design'];

        foreach ($categories as $category) {
            JobCategory::create([
                'name' => $category
            ]);
        }
    }
}
