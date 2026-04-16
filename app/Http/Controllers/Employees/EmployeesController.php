<?php

namespace App\Http\Controllers\Employees;

use App\Http\Requests\Employee\EmployeeStoreRequest;
use App\Models\Employee;
use App\Http\Requests\Employee\EmployeeUpdateRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmployeesController
{
    /**
     * Получить список всех сотрудников
     */
    public function index()
    {
        $employees = Employee::all();
        return $employees;
    }

    /**
     * Получить информацию о конкретном сотруднике
     */
    public function show($id)
    {
        $employee = Employee::find($id);
        
        if (!$employee) {
            throw new NotFoundHttpException("Not found");
        }

        return $employee;
    }

    /**
     * Создать нового сотрудника
     */
    public function store(EmployeeStoreRequest $request)
    {
        $employee = Employee::create($request->validated());
        return $employee;
    }

    /**
     * Обновить информацию о сотруднике
     */
    public function update(EmployeeUpdateRequest $request )
    {
        $id = $request->route("id");
        $employee = Employee::find($id);

         if (!$employee) {
            throw new NotFoundHttpException("Not found");
        }
        
        $employee->update($request->only(['employee_card_id', 'full_name', 'phone_number', 'position', 'division_id']));
        return $employee;
    }

    /**
     * Удалить сотрудника
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            throw new NotFoundHttpException("Not found");
        }

        $employee->delete();
        return $employee;
    }
}
