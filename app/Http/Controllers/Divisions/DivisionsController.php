<?php

namespace App\Http\Controllers\Divisions;

use App\Http\Requests\Division\DivisionStoreRequest;
use App\Http\Responses\Division\DivisionResponseSingle;
use App\Models\Division;
use App\Http\Requests\Division\DivisionUpdateRequest;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class DivisionsController
{
    /**
     * Получить список всех подразделений
     */
    public function index()
    {
        $divisions = Division::all();
        return new $divisions;
    }

    public function show($id)
    {
        $division = Division::find($id);
        
        if (!$division) {
            throw new NotFoundHttpException("Not found");
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
    public function update(DivisionUpdateRequest $request)
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
           throw new NotFoundHttpException('Not found');
        }

        $division->delete();

        return $division;
        
    }
}
