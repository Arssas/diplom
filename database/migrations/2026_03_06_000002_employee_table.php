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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('id'); // ID записи (PK)
            $table->string('card_id')->unique(); // ID Карты сотрудника
            $table->string('full_name'); // ФИО сотрудника
            $table->string('phone_number'); // Телефонный номер сотрудника
            $table->string('position'); // Должность сотрудника
            $table->unsignedBigInteger('division_id'); // Подразделение(FK)
            
            // Внешний ключ к таблице подразделений
            $table->foreign('division_id')
                  ->references('id')
                  ->on('divisions')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            // Индексы
            $table->index('card_id');
            $table->index('full_name');
            $table->index('division_id');
            // $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};