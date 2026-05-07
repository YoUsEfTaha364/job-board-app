<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeRequest extends FormRequest
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
            'file_name' => 'sometimes|required|string|max:255',
            'file_url' => 'sometimes|required|url|max:255',
            'contract_details' => 'sometimes|required|string|max:255',
            'skills' => 'sometimes|required|string',
            'summary' => 'sometimes|required|string',
            'experience' => 'sometimes|required|string',
            'education' => 'sometimes|required|string',
        ];
    }
}
