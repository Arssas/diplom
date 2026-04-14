<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DivisionsController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EventsController;

Route::controller(DivisionsController::class)->group(function () {
    Route::get('devisions/', 'index');
    Route::get('devisions/{id}', 'show');
    Route::post('devisions/','store');
    Route::put('devisions/{id}','update');
    Route::delete('devisions/{id}','destroy');
});

Route::controller(EmployeeController::class)->group(function () {
    Route::get('employee/', 'index');
    Route::get('employee/{id}', 'show');
    Route::post('employee/','store');
    Route::put('employee/{id}','update');
    Route::delete('employee/{id}','destroy');
});

Route::controller(EventsController::class)->group(function () {
    Route::get('event/', 'index');
    Route::get('event/{id}', 'show');
    Route::post('event/','store');
    Route::put('event/{id}','update');
    Route::delete('event/{id}','destroy');
});

Route::controller(EmployeeController::class)->group(function () {
    Route::get('report/', 'index');
});

