<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'divisions';
    protected $primaryKey = 'division_id';
    public $timestamps = false;
    protected $fillable = [
        'division_name',
        'manager_full_name'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'division_id', 'division_id');
    }
}                    