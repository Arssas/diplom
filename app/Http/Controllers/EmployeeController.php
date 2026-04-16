<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\EmployeeStoreRequest;
use App\Models\Employee;
use App\Http\Requests\Employee\EmployeeUpdateRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class EmployeeController extends Controller
{
    /**
     * Получить список всех событий
     */
    public function index()
    {
        $employee = Employee::all();
        return $employee;
        
    }

    /**
     * Получить информацию о конкретном событии
     */
    public function show($id)
    {
        $employee = Employee::find($id);
        
        if (!$employee) {
            throw new NotFoundHttpException("Сотрудник не найден");
        }

        return $employee;

    }

    /**
     * Создать новое сбытие
     */
    public function store(EmployeeStoreRequest $request)
    {
        $employee = Employee::create($request->validated());

        return $employee;
    }

    /**
     * Обновить информацию о событии
     */
    public function update(EmployeeUpdateRequest $request )
    {
        
        $id = $request->route("id");
        
        $employee = Employee::find($id);

         if (!$employee) {
            throw new NotFoundHttpException("Сотрудник не найден");
        }
        
        $employee->update($request->only(['employee_card_id', 'full_name', 'phone_number', 'position', 'division_id']));

        
        return $employee;
    }

    /**
     * Удалить событие
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            throw new NotFoundHttpException("Сотрудник не найден");
        }

        $employee->delete();

        return $employee;
    }
}
