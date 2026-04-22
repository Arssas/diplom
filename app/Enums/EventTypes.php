<?php
namespace App\Enums;

enum EventTypes:string{
    case Entry = 'entry';        // Вход
    case Exit = 'exit';         // Выход
    case BreakStart = 'break_start';  // Начало перерыва
    case BreakEnd = 'break_end';    // Конец перерыва
}