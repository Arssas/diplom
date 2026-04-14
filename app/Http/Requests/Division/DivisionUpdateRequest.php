<?php

namespace App\Http\Requests\Division;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DivisionUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        return [
            'division_name' => 'string|max:255|unique:divisions',
            'manager_full_name' => 'nullable|string|max:255'
        ];
    }
}
