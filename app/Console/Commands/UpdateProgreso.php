<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateProgreso extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyectos:update-progreso {--id=* : Optional project id(s) to limit the update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula y persiste el campo progreso para proyectos usando fecha_inicio_estimado y fecha_finalizacion.';

    public function handle()
    {
        $ids = $this->option('id');
        $query = DB::table('proyectos');
        if (!empty($ids)) {
            $query->whereIn('id_proyecto', $ids);
        }

        $proyectos = $query->select('id_proyecto','fecha_inicio_estimado','fecha_finalizacion','fecha_creacion')->get();
        $now = Carbon::now();
        $updated = 0;

        foreach ($proyectos as $p) {
            $startRaw = $p->fecha_inicio_estimado ?? $p->fecha_creacion ?? null;
            $endRaw = $p->fecha_finalizacion ?? null;
            $progress = 0;

            try {
                if ($startRaw && $endRaw) {
                    $start = Carbon::parse($startRaw);
                    $end = Carbon::parse($endRaw);
                    if ($end->lessThanOrEqualTo($start)) {
                        $progress = $now->greaterThanOrEqualTo($end) ? 100 : 0;
                    } else {
                        // usar timestamps directos para evitar signos raros en diffInSeconds
                        $total = max(1, intval($end->getTimestamp() - $start->getTimestamp()));
                        $done = $now->lessThanOrEqualTo($start) ? 0 : intval($now->getTimestamp() - $start->getTimestamp());
                        $done = max(0, min($total, $done));
                        $ratio = $total > 0 ? ($done / $total) : 0;
                        $progress = intval(max(0, min(100, floor($ratio * 100))));
                    }
                } elseif ($startRaw && !$endRaw) {
                    $start = Carbon::parse($startRaw);
                    $progress = $now->lessThan($start) ? 0 : 100;
                } elseif ($endRaw && !$startRaw) {
                    $end = Carbon::parse($endRaw);
                    $progress = $now->greaterThanOrEqualTo($end) ? 100 : 0;
                } else {
                    $progress = 0;
                }
            } catch (\Throwable $e) {
                Log::warning('UpdateProgreso parse error for proyecto '.$p->id_proyecto.': '.$e->getMessage());
                $progress = 0;
            }

            DB::table('proyectos')->where('id_proyecto', $p->id_proyecto)->update(['progreso' => $progress]);
            $this->info("Proyecto {$p->id_proyecto} -> progreso={$progress}");
            $updated++;
        }

        $this->info("Actualizados: {$updated} proyectos.");
        return 0;
    }
}
