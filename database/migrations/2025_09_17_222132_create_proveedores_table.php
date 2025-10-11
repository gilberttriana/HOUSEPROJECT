<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id('id_proveedor');
            $table->unsignedBigInteger('usuario_id');
            $table->string('empresa',150);
            $table->string('logo')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('proveedores');
    }
};
