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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('appointment_date'); // Fecha del apuntamento
            $table->time('appointment_time'); // Hora del apuntamento
            $table->unsignedBigInteger('client_id'); // Cliente relacionado con la tabla clients
            $table->foreign('client_id')->references('id')->on('clients');
            $table->string('status'); // Estado del appointment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

