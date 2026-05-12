<?php

namespace App\Http\Requests;

use App\Services\ApiResponseService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class FilterJobApplicationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
   public function rules(): array
{
    return [
        'status' => 'nullable|string|in:pending,accepted,rejected',

        'job_vacancy_id' => 'nullable|uuid|exists:job_vacancies,id',
        'job_vacancy_type' => 'nullable|in:part-time,full-time,hybrid,remote,cotract',
        'search' => 'nullable|string',
    ];
}
}
