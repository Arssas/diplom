<?php

namespace App\Http\Controllers;

use App\Enums\EventTypes;
use App\Http\Requests\Events\EventsStoreRequest;
use App\Models\Events;
use App\Http\Requests\Events\EventsUpdateRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventsController extends Controller
{
    /**
     * Получить список всех событий
     */
    public function index()
    {
        $event = Events::all();
        return $event;
        
    }

    /**
     * Получить информацию о конкретном событии
     */
    public function show($id)
    {
        $event = Events::find($id);
        
        if (!$event) {
            throw new NotFoundHttpException("событие не найдено");
        }

        return $event;

    }

    /**
     * Создать новое сбытие
     */
    public function store(EventsStoreRequest $request)
    {
        $event = Events::create($request->validated());

        return $event;
    }

    /**
     * Обновить информацию о событии
     */
    public function update(EventsUpdateRequest $request )
    {
        $id = $request->route("id");
        $event = Events::find($id);

        if ($event) {
            throw new NotFoundHttpException("событие не найдено");
        }
        $event->update($request->only(['employee_card_id', 'event_datetime', 'event_type']));

        return $event;
    }

    /**
     * Удалить событие
     */
    public function destroy($id)
    {
        $event = Events::find($id);

        if ($event) {
            throw new NotFoundHttpException("событие не найдено");
        }

        $event->delete();

        return $event;
    }
}
