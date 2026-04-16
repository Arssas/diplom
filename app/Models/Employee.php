<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'card_id',
        'full_name', 
        'phone_number',
        'position',
        'division_id'
    ];
}                    