<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Division::factory(2)->create();
        
        $divisions = Division::all();
        Employee::factory(10)->make()->each(function ($employee) use ($divisions) {
            $employee->division_id = $divisions->random()->id;
            $employee->save();
        });
    }
}
