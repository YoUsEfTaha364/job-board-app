<?php

namespace App\Http\Requests;

use App\Services\ApiResponseService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use PHPUnit\Event\Test\Failed;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;
class CreateCompanyRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'website' => 'nullable|string|url|max:255',
            'industry' => 'required|string|max:255',
            'owner_id' => ['required', Rule::exists('users', 'id'), Rule::unique('companies', 'owner_id')],
        ];
    }

    public function messages() {

       return [
        "owner_id.unique"=>"user already has company"
       ];
        
    }
}
