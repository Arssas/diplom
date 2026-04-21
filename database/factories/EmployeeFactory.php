<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'card_id'     => $this->faker->unique()->randomNumber(4, true),
            'full_name'   => $this->faker->name(),
            'phone_number'=> $this->faker->phoneNumber(),
            'position'    => $this->faker->jobTitle(),
        ];
    }
}
