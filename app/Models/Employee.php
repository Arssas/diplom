<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

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