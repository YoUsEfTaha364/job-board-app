<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobApplicationRequest extends FormRequest
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
            'status' => 'sometimes|in:pending,accepted,rejected',
            'ai_generated_score' => 'sometimes|numeric|min:0|max:100',
            'ai_generated_feedback' => 'sometimes|string',
            'job_vacancy_id' => 'sometimes|exists:job_vacancies,id',
            'resume_id' => 'sometimes|exists:resumes,id',
        ];
    }
}
