<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'employee_card_id',
        'datetime',
        'type',
    ];
}                    