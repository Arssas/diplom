<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;

class ResponseWithData extends JsonResource
{
    public function rules(bool $success=true, $data): array
    {   
        return [
            "success" => $success,
            "data" => $data
        ];
    }
}