<?php

namespace App\Http\Requests\Events;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\EventTypes;
class EventsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {   
        return [
            'event_type' => ['required', Rule::enum(EventTypes::class)],
            'employee_card_id' => 'integer|max:100|required',
            'event_datetime' =>  'required|date'
        ];
    }
}