<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resume;
use App\Models\User;

class ResumeSeeder extends Seeder
{
    public function run(): void
    {
        $seekers = User::where('role', 'job-seeker')->get();

        foreach ($seekers as $index => $seeker) {
            Resume::create([
                'file_name' => 'resume_' . $seeker->id . '.pdf',
                'file_url' => 'https://example.com/resumes/' . $seeker->id . '.pdf',
                'contract_details' => '+1234567890',
                'skills' => 'PHP, Laravel, JavaScript, HTML, CSS',
                'summary' => 'A passionate software developer.',
                'experience' => '2 years of experience in web development.',
                'education' => 'Bachelor of Science in Computer Science',
                'user_id' => $seeker->id,
            ]);
        }
    }
}
