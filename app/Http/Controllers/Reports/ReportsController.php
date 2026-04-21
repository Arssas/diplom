<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Events;

class ReportsController 
{
    /**
     * Получить полностью структурированные данные со всех таблиц
     * GET /api/structure
     */
    public function getStructuredData()
    {
        // Получаем все подразделения с их сотрудниками
        $divisions = Division::with('employees')->get();
        
        // Получаем все события
        $events = Events::all();
        
        // Формируем итоговую структуру
        $structuredData = [
            'divisions' => $divisions->map(function ($division) {
                return [
                    'id' => $division->division_id,
                    'name' => $division->division_name,
                    'manager_full_name' => $division->manager_full_name,
                    'employees_count' => $division->employees->count(),
                    'employees' => $division->employees->map(function ($employee) {
                        return [
                            'id' => $employee->employee_id,
                            'full_name' => $employee->full_name,
                            'position' => $employee->position,
                            'phone_number' => $employee->phone,
                        ];
                    })
                ];
            }),
            
            'employees' => Employee::all()->map(function ($employee) {
                return [
                    'id' => $employee->employee_id,
                    'full_name' => $employee->full_name,
                    'position' => $employee->position,
                    'division_id' => $employee->division_id,
                    'phone_number' => $employee->phone,
                ];
            }),
            
            'events' => $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'employee_card_id' => $event->employee_card_id,
                    'datetime' => $event->datetime,
                    'type' => $event->type
                ];
            })
            
       ];
        
        return response()->json([
            'success' => true,
            'data' => $structuredData
        ]);
    }
    
    /**
     * Получить структурированные данные только по сотрудникам и их подразделениям
     * GET /api/structure/employees
     */
    public function getEmployeesStructure()
    {
        $divisions = Division::with('employees')->get();
        
        $data = $divisions->map(function ($division) {
            return [
                'id' => $division->division_id,
                'name' => $division->division_name,
                'manager_full_name' => $division->manager_full_name,
                'employees_count' => $division->employees->count(),
                'employees' => $division->employees->map(function ($employee) {
                    return [
                        'id' => $employee->employee_id,
                        'full_name' => $employee->full_name,
                        'position' => $employee->position,
                        'phone_number' => $employee->phone,
                    ];
                })
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Получить структурированные данные только по событиям
     * GET /api/structure/events
     */
    public function getEventsStructure()
    {
        // Группируем события по типу
        $eventsByType = Events::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();
        
        // Получаем последние 50 событий
        $recentEvents = Events::orderBy('datetime', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'employee_card_id' => $event->employee_card_id,
                    'datetime' => $event->datetime,
                    'type' => $event->type
                ];
            });
        
        $data = [
            'summary' => $eventsByType,
            'recent_events' => $recentEvents,
            'total_events' => Events::count()
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}