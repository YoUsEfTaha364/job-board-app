<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|in:pending,accepted,rejected',
            'ai_generated_score' => 'nullable|numeric|min:0|max:100',
            'ai_generated_feedback' => 'nullable|string',
            'job_vacancy_id' => 'required|exists:job_vacancies,id',
            'resume_id' => 'required|exists:resumes,id',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
