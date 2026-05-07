<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
         $categoryId = $this->route('category');
         
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                "unique:job_categories,name,{$categoryId}",
            ],
        ];
    }
}
