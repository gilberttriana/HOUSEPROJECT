<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * We modify the `contratista` column to VARCHAR(255) so it can store names.
     * Use raw SQL to avoid requiring doctrine/dbal for this small change.
     */
    public function up()
    {
        DB::statement("ALTER TABLE `proyectos` MODIFY `contratista` VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * Revert to DATE (as it was created por accidente). If you prefer DATETIME change accordingly.
     */
    public function down()
    {
        DB::statement("ALTER TABLE `proyectos` MODIFY `contratista` DATE NULL");
    }
};
