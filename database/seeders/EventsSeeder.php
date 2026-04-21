<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Events;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();

        $startDate = Carbon::today()->subDays(30);
        $endDate = Carbon::today();

        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }

                $this->generateEventsForDay($employee, $currentDate);
                $currentDate->addDay();
            }
        }
    }

    private function generateEventsForDay(Employee $employee, Carbon $date): void
    {
        $startOfWork = $date->copy()->setTime(9, 0, 0);
        $endOfWork = $date->copy()->setTime(18, 0, 0);

        $events = [
            [
                'employee_card_id' => $employee->card_id,
                'datetime'         => $startOfWork,
                'type'             => 'entry',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ];

        $hasBreak = rand(1, 100) <= 70;

        if ($hasBreak) {
            $breakStartTime = $date->copy()->setTime(rand(10, 16), rand(0, 59), 0);
            $breakDurationMinutes = rand(15, 60);
            $breakEndTime = $breakStartTime->copy()->addMinutes($breakDurationMinutes);

            if ($breakEndTime->lte($endOfWork) && $breakStartTime->gte($startOfWork)) {
                $events[] = [
                    'employee_card_id' => $employee->card_id,
                    'datetime'         => $breakStartTime,
                    'type'             => 'break_start',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
                $events[] = [
                    'employee_card_id' => $employee->card_id,
                    'datetime'         => $breakEndTime,
                    'type'             => 'break_end',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }

        $events[] = [
            'employee_card_id' => $employee->card_id,
            'datetime'         => $endOfWork,
            'type'             => 'exit',
            'created_at'       => now(),
            'updated_at'       => now(),
        ];

        usort($events, function ($a, $b) {
            return $a['datetime'] <=> $b['datetime'];
        });

        foreach ($events as $event) {
            Events::create($event);
        }
    }
}
