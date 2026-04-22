<?php

namespace App\Http\Controllers\Reports;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Events;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReportsController 
{
    /**
     * Задача 1: Получить всю информацию по конкретному сотруднику за конкретную дату
     * GET /api/report/employee/{employee_id}/{date}
     * 
     * @param string $employee_card_id - ID карты сотрудника
     * @param string $date - дата в формате YYYY-MM-DD
     */
    public function getEmployeeDailyReport($employee_card_id, $date)
    {
        // Проверяем формат даты
        $validator = Validator::make(['date' => $date, 'employee_card_id' => $employee_card_id], [
            'date' => 'required|date_format:Y-m-d',
            'employee_card_id' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Ищем сотрудника
        $employee = Employee::where('card_id', $employee_card_id)->first();
        
        if (!$employee) {
            throw new NotFoundHttpException("Сотрудник не найден");
        }
        
        $startOfDay = $date . ' 00:00:00';
        $endOfDay = $date . ' 23:59:59';

        // Получаем все события сотрудника за указанную дату
        $events = Events::where('employee_card_id', $employee->card_id)
            ->whereBetween('datetime', [$startOfDay, $endOfDay])
            ->orderBy('datetime', 'asc')
            ->get();
        
        if ($events->count() == 0) {
            throw new NotFoundHttpException("Событий нет");
        }

        // Получаем подразделение сотрудника
        $division = Division::find($employee->division_id);
        
        // Формируем структурированный ответ
        $result = [
            'employee' => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'position' => $employee->position,
                'division' => $division ? [
                    'id' => $division->id,
                    'name' => $division->name,
                    'manager_full_name' => $division->manager_full_name
                ] : null,
                'phone_number' => $employee->phone_number,
            ],
            'date' => $date,
            'events' => $events->map(function ($event) {
                return [
                    'time' => date('H:i:s', strtotime($event->datetime)),
                    'type' => $event->type,
                    'type_label' => $this->getEventTypeLabel($event->type)
                ];
            }),
            'events_count' => $events->count(),
            'summary' => $this->calculateDailySummary($events)
        ];
        
        return $result;
    }
    
    /**
     * Задача 2: Посчитать количество отработанных часов за месяц
     * GET /api/report/getWorkedHoursPerMonth/{card_id}/{year}/{month}
     * 
     * @param int $card_id - ID сотрудника
     * @param int $year - год (например, 2026)
     * @param int $month - месяц (1-12)
     */
    public function getWorkedHoursPerMonth($card_id, $year, $month)
    {
        // Проверяем валидность параметров
        $validator = Validator::make([
            'card_id' => $card_id,
            'year' => $year,
            'month' => $month
        ], [
            'card_id' => 'required|string',
            'year' => 'required|string',
            'month' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Ищем сотрудника
        $employee = Employee::where("card_id", $card_id)->first();
        
        if (!$employee) {
            throw new NotFoundHttpException("Сотрудник не найден");
        }
        
        // Определяем начало и конец месяца
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Получаем все события сотрудника за месяц
        $events = Events::where('employee_card_id', $employee->card_id)
            ->whereBetween('datetime', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('datetime', 'asc')
            ->get();
        
        if ($events->count() == 0) {
            throw new NotFoundHttpException("Событий нет");
        }

        // Группируем события по дням
        $eventsByDay = $events->groupBy(function ($event) {
            return date('Y-m-d', strtotime($event->datetime));
        });
        
        // Рассчитываем отработанные часы по каждому дню и суммарно
        $dailyHours = [];
        $totalWorkedSeconds = 0;
        
        foreach ($eventsByDay as $day => $dayEvents) {
            $dayResult = $this->calculateDailyWorkedHours($dayEvents);
            $dailyHours[] = [
                'date' => $day,
                'worked_hours' => round($dayResult['hours'], 2),
                'worked_minutes' => $dayResult['minutes'],
                'first_entry' => $dayResult['first_entry'],
                'last_exit' => $dayResult['last_exit']
            ];
            $totalWorkedSeconds += $dayResult['seconds'];
        }
        
        $totalWorkedHours = floor($totalWorkedSeconds / 3600);
        $totalWorkedMinutes = floor(($totalWorkedSeconds % 3600) / 60);
        
        // Получаем подразделение сотрудника
        $division = Division::find($employee->division_id);
        
        $result = [
            'employee' => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'position' => $employee->position,
                'division' => $division ? $division->division_name : null
            ],
            'period' => [
                'year' => $year,
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'summary' => [
                'total_worked_hours' => $totalWorkedHours,
                'total_worked_minutes' => $totalWorkedMinutes,
                'total_worked_hours_decimal' => round($totalWorkedSeconds / 3600, 2),
                'days_worked' => count($dailyHours),
                'total_days_in_month' => date('t', strtotime($startDate))
            ],
            'daily_breakdown' => $dailyHours
        ];
        
        return $result;
    }
    
    /**
     * Задача 3: Посчитать количество сверхурочных часов за месяц
     * GET /api/report/employee/{employee_id}/overtime/month/{year}/{month}
     * 
     * @param int $employee_id - ID сотрудника
     * @param int $year - год (например, 2026)
     * @param int $month - месяц (1-12)
     */
    public function getOvertimeHoursPerMonth($employee_id, $year, $month)
    {
        // Проверяем валидность параметров
        $validator = Validator::make([
            'employee_id' => $employee_id,
            'year' => $year,
            'month' => $month
        ], [
            'employee_id' => 'required|integer|exists:employees,employee_id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Ищем сотрудника
        $employee = Employee::find($employee_id);
        
        if (!$employee) {
            throw new NotFoundHttpException("Сотрудник не найден");
        }
        
        // Норма рабочих часов в месяц (стандартно 8 часов в день, 5-дневная неделя)
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Рассчитываем норму часов (рабочие дни * 8 часов)
        $workDays = $this->getWorkDaysCount($year, $month);
        $standardHours = $workDays * 8;
        
        // Получаем все события сотрудника за месяц
        $events = Events::where('employee_card_id', $employee->access_card_number)
            ->whereBetween('datetime', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('datetime', 'asc')
            ->get();
        
        // Группируем события по дням
        $eventsByDay = $events->groupBy(function ($event) {
            return date('Y-m-d', strtotime($event->datetime));
        });
        
        // Рассчитываем фактически отработанные часы
        $totalWorkedSeconds = 0;
        $dailyOvertime = [];
        
        foreach ($eventsByDay as $day => $dayEvents) {
            $dayResult = $this->calculateDailyWorkedHours($dayEvents);
            $dayWorkedHours = $dayResult['seconds'] / 3600;
            
            // Сверхурочные за день (то, что сверх 8 часов)
            $dayOvertimeHours = max(0, $dayWorkedHours - 8);
            $dailyOvertime[] = [
                'date' => $day,
                'worked_hours' => round($dayWorkedHours, 2),
                'overtime_hours' => round($dayOvertimeHours, 2),
                'is_weekend' => $this->isWeekend($day)
            ];
            
            $totalWorkedSeconds += $dayResult['seconds'];
        }
        
        $actualHours = $totalWorkedSeconds / 3600;
        $overtimeHours = max(0, $actualHours - $standardHours);
        
        // Дополнительно: находим события типа overtime_start и overtime_end
        $overtimeEvents = $events->filter(function ($event) {
            return $event->type === 'overtime_start' || $event->type === 'overtime_end';
        });
        
        $registeredOvertimeSeconds = $this->calculateOvertimeFromEvents($overtimeEvents);
        $registeredOvertimeHours = $registeredOvertimeSeconds / 3600;
        
        $division = Division::find($employee->division_id);
        
        $result = [
            'employee' => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'position' => $employee->position,
                'division' => $division ? $division->division_name : null
            ],
            'period' => [
                'year' => $year,
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'work_days_in_month' => $workDays,
                'standard_hours' => $standardHours
            ],
            'summary' => [
                'actual_worked_hours' => round($actualHours, 2),
                'standard_hours' => $standardHours,
                'overtime_hours' => round($overtimeHours, 2),
                'overtime_minutes' => round($overtimeHours * 60, 0),
                'registered_overtime_from_events' => round($registeredOvertimeHours, 2)
            ],
            'daily_breakdown' => $dailyOvertime
        ];
        
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
    
    /**
     * Вспомогательный метод: расчёт дневной сводки по событиям
     */
    private function calculateDailySummary($events)
    {
        $firstEntry = null;
        $lastExit = null;
        
        foreach ($events as $event) {
            if ($event->type === 'entry' && !$firstEntry) {
                $firstEntry = $event->datetime;
            }
            if ($event->type === 'exit') {
                $lastExit = $event->datetime;
            }
        }
        
        return [
            'first_entry' => $firstEntry ? date('H:i:s', strtotime($firstEntry)) : null,
            'last_exit' => $lastExit ? date('H:i:s', strtotime($lastExit)) : null
        ];
    }
    
    /**
     * Вспомогательный метод: расчёт отработанных часов за день на основе событий
     */
    private function calculateDailyWorkedHours($dayEvents)
    {
        $seconds = 0;
        $lastEntryTime = null;
        $firstEntry = null;
        $lastExit = null;
        
        foreach ($dayEvents as $event) {
            $eventTime = strtotime($event->datetime);
            
            if ($event->type === 'entry') {
                $lastEntryTime = $eventTime;
                if (!$firstEntry) {
                    $firstEntry = $event->datetime;
                }
            } elseif ($event->type === 'exit' && $lastEntryTime) {
                $seconds += ($eventTime - $lastEntryTime);
                $lastExit = $event->datetime;
                $lastEntryTime = null;
            } elseif ($event->type === 'break_start' && $lastEntryTime) {
                // Если начался перерыв, вычитаем время из рабочего
                $lastEntryTime = null;
            } elseif ($event->type === 'break_end') {
                // Перерыв закончился, начинаем снова учитывать время
                $lastEntryTime = $eventTime;
            }
        }
        
        $hours = $seconds / 3600;
        $minutes = floor($seconds / 60);
        
        return [
            'seconds' => $seconds,
            'minutes' => $minutes,
            'hours' => $hours,
            'first_entry' => $firstEntry ? date('H:i:s', strtotime($firstEntry)) : null,
            'last_exit' => $lastExit ? date('H:i:s', strtotime($lastExit)) : null
        ];
    }
    
    /**
     * Вспомогательный метод: расчёт сверхурочных из событий overtime_start/overtime_end
     */
    private function calculateOvertimeFromEvents($overtimeEvents)
    {
        $seconds = 0;
        $lastStart = null;
        
        foreach ($overtimeEvents as $event) {
            $eventTime = strtotime($event->datetime);
            
            if ($event->type === 'overtime_start') {
                $lastStart = $eventTime;
            } elseif ($event->type === 'overtime_end' && $lastStart) {
                $seconds += ($eventTime - $lastStart);
                $lastStart = null;
            }
        }
        
        return $seconds;
    }
    
    /**
     * Вспомогательный метод: получить количество рабочих дней в месяце
     */
    private function getWorkDaysCount($year, $month)
    {
        $daysInMonth = date('t', strtotime("{$year}-{$month}-01"));
        $workDays = 0;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = "{$year}-{$month}-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            if (!$this->isWeekend($date)) {
                $workDays++;
            }
        }
        
        return $workDays;
    }
    
    /**
     * Вспомогательный метод: проверить, является ли дата выходным днём (суббота/воскресенье)
     */
    private function isWeekend($date)
    {
        $dayOfWeek = date('N', strtotime($date));
        return $dayOfWeek >= 6;
    }
    
    /**
     * Вспомогательный метод: получить название месяца на русском
     */
    private function getMonthName($month)
    {
        $months = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
            5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
            9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];
        
        return $months[$month] ?? '';
    }
    
    /**
     * Вспомогательный метод: получить человеко-читаемую метку типа события
     */
    private function getEventTypeLabel($type)
    {
        $labels = [
            'entry' => 'Вход',
            'exit' => 'Выход',
            'break_start' => 'Начало перерыва',
            'break_end' => 'Конец перерыва',
            'overtime_start' => 'Начало сверхурочной работы',
            'overtime_end' => 'Конец сверхурочной работы'
        ];
        
        return $labels[$type] ?? $type;
    }
}