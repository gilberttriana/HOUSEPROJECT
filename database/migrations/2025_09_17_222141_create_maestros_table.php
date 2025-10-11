<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('maestros', function (Blueprint $table) {
            $table->id('id_maestro');
            $table->unsignedBigInteger('usuario_id');
            $table->string('especialidad',100);
            $table->text('experiencia')->nullable();
            $table->decimal('tarifa',10,2)->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('maestros');
    }
};
