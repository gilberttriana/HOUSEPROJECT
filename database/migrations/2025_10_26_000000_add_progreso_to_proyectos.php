<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddProgresoToProyectos extends Migration
{
    /**
     * Run the migrations.
     * Adds the `progreso` integer column and backfills computed values (0-100).
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('proyectos', 'progreso')) {
            Schema::table('proyectos', function (Blueprint $table) {
                $table->integer('progreso')->default(0)->after('estado');
            });

            // Backfill computed progress using fecha_inicio_estimado/fecha_finalizacion/created_at
            try {
                $now = Carbon::now();
                $proyectos = DB::table('proyectos')->select('id_proyecto', 'fecha_inicio_estimado', 'fecha_finalizacion', 'created_at')->get();

                foreach ($proyectos as $p) {
                    $startRaw = $p->fecha_inicio_estimado ?? $p->created_at ?? null;
                    $endRaw = $p->fecha_finalizacion ?? null;

                    $progress = 0;

                    if ($startRaw && $endRaw) {
                        try {
                            $start = Carbon::parse($startRaw);
                            $end = Carbon::parse($endRaw);

                            if ($end->lessThanOrEqualTo($start)) {
                                $progress = ($now->greaterThanOrEqualTo($end)) ? 100 : 0;
                            } else {
                                $total = $end->diffInSeconds($start);
                                $done = min($now->diffInSeconds($start), $total);
                                $progress = (int) floor(($done / max(1, $total)) * 100);
                                $progress = max(0, min(100, $progress));
                            }
                        } catch (\Exception $ex) {
                            $progress = 0;
                        }
                    } elseif ($startRaw && !$endRaw) {
                        // If only start exists, mark as 0 if start in future, otherwise 100 (can't know duration)
                        try {
                            $start = Carbon::parse($startRaw);
                            $progress = $now->lessThan($start) ? 0 : 100;
                        } catch (\Exception $ex) {
                            $progress = 0;
                        }
                    }

                    DB::table('proyectos')->where('id_proyecto', $p->id_proyecto)->update(['progreso' => $progress]);
                }
            } catch (\Exception $e) {
                // don't break migrations if backfill fails; column is still present
                logger()->warning('No se pudo backfillear progreso en migración: '.$e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('proyectos', 'progreso')) {
            Schema::table('proyectos', function (Blueprint $table) {
                $table->dropColumn('progreso');
            });
        }
    }
}
