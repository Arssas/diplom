<?php

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {   
        return [
            'employee_card_id' => 'integer|max:100|required|unique:employees',
            'full_name' => 'string|max:100|required', 
            'phone_number' => 'string|max:100|required|unique:employees',
            'position' => 'string|max:100',
            'division_id' => 'integer|max:100|required'
        ];
    }
}
