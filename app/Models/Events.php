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
        'event_datetime',
        'event_type',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'event_id', 'event_id');
    }
}                    