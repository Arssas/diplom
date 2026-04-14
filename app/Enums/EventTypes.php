<?php
namespace App\Enums;

enum EventTypes:string{
    case Entry='entry';        // Вход
    case Exit = 'exit';         // Выход
    case BreakStart = 'break_start';  // Начало перерыва
    case BreakEnd = 'break_end';    // Конец перерыва
    case OvertimeStart = 'overtime_start'; // Начало сверхурочной работы
    case OvertimeEnd = 'overtime_end'; // Конец сверхурочной работы

}