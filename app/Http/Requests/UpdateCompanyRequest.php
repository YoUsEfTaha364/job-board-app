<?php

namespace App\Http\Requests;

use App\Services\ApiResponseService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class UpdateCompanyRequest extends FormRequest
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
        throw new HttpResponseException(ApiResponseService::Response(404,"attributes error",$validator->errors()));

    }
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'website' => 'nullable|string|url|max:255',
            'industry' => 'sometimes|required|string|max:255',
        ];
    }
}
