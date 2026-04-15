<?php

namespace App\Http\Controllers;

use App\Http\Requests\Division\DivisionStoreRequest;
use App\Models\Division;
use App\Http\Requests\Division\DivisionUpdateRequest;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\RecordNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class DivisionsController extends Controller
{
    /**
     * Получить список всех подразделений
     */
    public function index()
    {
        $divisions = Division::all();
        return response()->json([
            'success' => true,
            'data' => $divisions
        ]);
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

        return response()->json($division, 201);
    }

    /**
     * Обновить информацию о подразделении
     */
    public function update(DivisionUpdateRequest $request )
    {
        $id = $request->route("id");
        $division = Division::find($id);

        $division->update($request->only(['division_name', 'manager_full_name']));

        return response()->json($division,201);
    }

    /**
     * Удалить подразделение
     */
    public function destroy($id)
    {
        $division = Division::find($id);

        if (!$division) {
            return response()->json([
                'success' => false,
                'message' => 'Подразделение не найдено'
            ], 404);
        }

        // Проверяем, есть ли сотрудники в этом подразделении
        if ($division->employees()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить подразделение, в котором есть сотрудники'
            ], 400);
        }

        $division->delete();

        return response()->json([
            'success' => true,
            'message' => 'Подразделение успешно удалено'
        ]);
    }
}
