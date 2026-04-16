<?php

namespace App\Http\Requests\Division;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DivisionStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|max:100|required|unique:divisions',
            'manager_full_name' => 'string|max:100|required'
        ];
    }
}
