<?php

use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Reports\ReportsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Divisions\DivisionsController;
use App\Http\Controllers\Events\EventsController;


Route::controller(DivisionsController::class)->prefix('divisions')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/','store');
    Route::put('/{id}','update');
    Route::delete('/{id}','destroy');
});

Route::controller(EmployeesController::class)->prefix('employees')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/','store');
    Route::put('/{id}','update');
    Route::delete('/{id}','destroy');
});

Route::controller(EventsController::class)->prefix('events')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/','store');
    Route::put('/{id}','update');
    Route::delete('/{id}','destroy');
});

Route::controller(ReportsController::class)->group(function () {
    Route::get('/getEmployeeDailyReport/{id}/{date}', 'getEmployeeDailyReport');
    Route::get('/getWorkedHoursPerMonth/{id}/{year}/{month}', 'getWorkedHoursPerMonth');
    Route::get('/getOvertimeHoursPerMonth', 'getOvertimeHoursPerMonth');
});

