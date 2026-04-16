<?php

namespace App\Http\Controllers;

use App\Http\Requests\Division\DivisionStoreRequest;
use App\Models\Division;
use App\Http\Requests\Division\DivisionUpdateRequest;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class DivisionsController extends Controller
{
    /**
     * Получить список всех подразделений
     */
    public function index()
    {
        $divisions = Division::all();
        return $divisions;
    }

    /**
     * Получить информацию о конкретном подразделении
     */
    public function show($id): Division
    {
        $division = Division::find($id);
        
        if (!$division) {
            throw new NotFoundHttpException("Not found ex");
        }

        return $division;
    }

    /**
     * Создать новое подразделение
     */
    public function store(DivisionStoreRequest $request)
    {
       $division = Division::create($request->validated());

        return $division;
    }

    /**
     * Обновить информацию о подразделении
     */
    public function update(DivisionUpdateRequest $request )
    {
        $id = $request->route("id");
        $division = Division::find($id);

        $division->update($request->only(['division_name', 'manager_full_name']));

        return $division;
    }

    /**
     * Удалить подразделение
     */
    public function destroy($id)
    {
        $division = Division::find($id);

        if (!$division) {
           throw new NotFoundHttpException('Не найдено');
        }

        $division->delete();

        return $division;
        
    }
}
