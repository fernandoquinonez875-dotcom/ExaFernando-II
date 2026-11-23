<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('marca');
            $table->date('fecha_adquisicion');
            $table->enum('estado', ['disponible', 'asignado', 'mantenimiento']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
