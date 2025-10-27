<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('proyectos', 'fecha_finalizacion')) {
            Schema::table('proyectos', function (Blueprint $table) {
                $table->dateTime('fecha_finalizacion')->nullable()->after('fecha_inicio_estimado');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('proyectos', 'fecha_finalizacion')) {
            Schema::table('proyectos', function (Blueprint $table) {
                $table->dropColumn('fecha_finalizacion');
            });
        }
    }
};
