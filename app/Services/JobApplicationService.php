<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Resume;
use Illuminate\Support\Facades\Auth;

class JobApplicationService
{

    protected GeminService $gemini;

    public function __construct(GeminService $service)
    {
        $this->gemini = $service;
    }

    public function storeJob($data)
    {
        $prompt = $this->prompt($data);

        

        $response=$this->gemini->Response($prompt);

        $text = $response["candidates"][0]["content"]["parts"][0]["text"] ?? '{}';
        
        // Sometimes the AI wraps it in markdown despite our prompt, so we strip it.
        $text = str_replace(['```json', '```'], '', $text);
        
        $aiData = json_decode(trim($text), true) ?? [];

        

        // Save the job application with the AI score and feedback
        $jobApplication = JobApplication::create([
            'job_vacancy_id' => $data['job_vacancy_id'],
            'resume_id' => $data['resume_id'],
            'user_id' => Auth::user()->id,
            'ai_generated_score' => $aiData['ai_generated_score'] ?? 0,
            'ai_generated_feedback' => $aiData['ai_feedback'] ?? 'No feedback provided.',
        ]);

        return $jobApplication;
    }

    private function prompt(array $data)
    {
        $job_vacancy = JobVacancy::find($data["job_vacancy_id"]);

        $job_data = [
            "job_title" => $job_vacancy->title,
            "job_description" => $job_vacancy->description
        ];

        $resume = Resume::find($data["resume_id"]);

        $resume_data = [
            "resume_skills" => $resume->skills,
            "resume_experience" => $resume->experience,
            "resume_education" => $resume->education,
            "resume_summary" => $resume->summary,
        ];


        $prompt = "
You are an AI recruitment assistant.

Your task is to analyze how well the candidate resume matches the job vacancy.

Return ONLY valid JSON.

Required JSON structure:
{
  \"ai_generated_score\": number,
  \"ai_feedback\": string
}

Rules:
- ai_generated_score must be a number from 0 to 100
- ai_feedback must explain:
  - strengths of the candidate
  - missing skills or experience
  - overall suitability for the job
- Keep feedback concise and professional
- Do not include markdown
- Do not include extra text outside JSON

Job Vacancy:
Title: {$job_data['job_title']}

Description:
{$job_data['job_description']}

Candidate Resume:

Summary:
{$resume_data['resume_summary']}

Skills:
" . json_encode($resume_data['resume_skills']) . "

Experience:
" . json_encode($resume_data['resume_experience']) . "

Education:
" . json_encode($resume_data['resume_education']) . "
";

        return $prompt;
    }
}
