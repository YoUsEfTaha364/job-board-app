<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\ApiResponseService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateResumeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiResponseService::Response(404, "attributes error", $validator->errors()));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_name' => 'required|string|max:255',
            'file_url' => 'required|url|max:255',
            'contract_details' => 'required|string|max:255',
            'skills' => 'required|string',
            'summary' => 'nullable|string',
            'experience' => 'nullable|string',
            'education' => 'nullable|string',
        ];
    }
}
