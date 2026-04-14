<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\EmployeeStoreRequest;
use App\Models\Employee;
use App\Http\Requests\Employee\EmployeeUpdateRequest;


class EmployeeController extends Controller
{
    /**
     * Получить список всех событий
     */
    public function index()
    {
        $employee = Employee::all();
        return response()->json([
            'success' => true,
            'data'=> $employee
        ]);
    }

    /**
     * Получить информацию о конкретном событии
     */
    public function show($id)
    {
        $employee = Employee::find($id);
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Событие не найдено'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }

    /**
     * Создать новое сбытие
     */
    public function store(EmployeeStoreRequest $request)
    {
        $employee = Employee::create($request->validated());

        return response()->json($employee, 201);
    }

    /**
     * Обновить информацию о событии
     */
    public function update(EmployeeUpdateRequest $request )
    {
        $id = $request->route("id");
        $employee = Employee::find($id);

        $employee->update($request->only(['employee_card_id', 'full_name', 'phone_number', 'position', 'division_id']));

        return response()->json($employee,201);
    }

    /**
     * Удалить событие
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'событие не найдено'
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'событие успешно удалено'
        ]);
    }
}
