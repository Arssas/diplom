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
        Schema::create('divisions', function (Blueprint $table) {
            $table->id('division_id'); // ID подразделения (PK)
            $table->string('division_name'); // Название отдела
            $table->string('manager_full_name')->nullable(); // ФИО руководителя
            
            //Индексы
            $table->index('division_name');
            $table->index('manager_full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};