<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id('id'); // ID События (PK)
            $table->string('employee_card_id'); // ID карты сотрудника (FK)
            $table->datetime('datetime'); // Дата и время
            $table->enum('type', [
                'entry',        // Вход
                'exit',         // Выход
                'break_start',  // Начало перерыва
                'break_end',    // Конец перерыва
                'overtime_start', // Начало сверхурочной работы
                'overtime_end'    // Конец сверхурочной работы
            ]); // Тип события
            
            $table->timestamps();
            
            // Внешний ключ к таблице табеля сотрудников (по ID карты)
            $table->foreign('employee_card_id')
                  ->references('card_id')
                  ->on('employees')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            // Индексы
            $table->index('employee_card_id');
            $table->index('datetime');
            $table->index('type');
            
            // Составной индекс для частых запросов по карте и дате
            $table->index(['employee_card_id', 'datetime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
