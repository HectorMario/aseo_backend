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
        Schema::create('appointment_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id'); // Referencia al appointment
            $table->foreign('appointment_id')->references('id')->on('appointments');
            $table->unsignedBigInteger('assignee_id'); // Referencia al encargado
            $table->foreign('assignee_id')->references('id')->on('assignees'); // Puedes tener una tabla 'assignees' para almacenar información sobre los encargados
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_assignments');
    }
};