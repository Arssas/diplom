<?php

namespace App\Http\Controllers;

use App\Enums\EventTypes;
use App\Http\Requests\Events\EventsStoreRequest;
use App\Models\Events;
use App\Http\Requests\Events\EventsUpdateRequest;


class EventsController extends Controller
{
    /**
     * Получить список всех событий
     */
    public function index()
    {
        $event = Events::all();
        return response()->json([
            'success' => true,
            'data'=> $event
        ]);
    }

    /**
     * Получить информацию о конкретном событии
     */
    public function show($id)
    {
        $event = Events::find($id);
        
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Событие не найдено'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    /**
     * Создать новое сбытие
     */
    public function store(EventsStoreRequest $request)
    {
        $event = Events::create($request->validated());

        return response()->json($event, 201);
    }

    /**
     * Обновить информацию о событии
     */
    public function update(EventsUpdateRequest $request )
    {
        $id = $request->route("id");
        $event = Events::find($id);

        $event->update($request->only(['employee_card_id', 'event_datetime', 'event_type']));

        return response()->json($event,201);
    }

    /**
     * Удалить событие
     */
    public function destroy($id)
    {
        $event = Events::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'событие не найдено'
            ], 404);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'событие успешно удалено'
        ]);
    }
}
