<?php

namespace App\Http\Requests;

use App\Services\ApiResponseService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class FilterJobVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
{
    $allowed = [
        'status',
        'type',
        'category_id',
        'company_id',
        'min_salary',
        'max_salary',
        'start_date',
        'end_date',
        'search',
    ];

    $unknownKeys = array_diff(
        array_keys($this->all()),
        $allowed
    );

    if (!empty($unknownKeys)) {
        abort(ApiResponseService::Response(404, "attributes error", []));
    }
}

       protected function failedValidation(Validator $validator)
    {
          throw new HttpResponseException(ApiResponseService::Response(404, "attributes error", $validator->errors()));
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|string',
            'type' => 'sometimes|string',

            'category_id' => 'sometimes|exists:job_categories,id',
            'company_id' => 'sometimes|exists:companies,id',

            'min_salary' => 'sometimes|numeric|min:0',
            'max_salary' => 'sometimes|numeric|min:0',

            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',

            'search' => 'sometimes|string|max:255',
        ];
    }
}