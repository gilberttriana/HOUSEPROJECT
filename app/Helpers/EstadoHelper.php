<?php

namespace App\Helpers;

class EstadoHelper
{
    /**
     * Normaliza un valor de estado y devuelve label y clase CSS.
     * @param mixed $raw
     * @return array ['label' => string, 'class' => string]
     */
    public static function normalize($raw)
    {
        $s = strtolower(trim((string)($raw ?? '')));
        // En espera / pendiente
        if ($s === '' || strpos($s, 'esper') !== false || strpos($s, 'pend') !== false) {
            return ['label' => 'En espera', 'class' => 'bg-yellow-500/20 text-yellow-500'];
        }
        // Desaprobado / rechazado (revisar antes de 'apro' porque contiene 'apro')
        if (strpos($s, 'desap') !== false || strpos($s, 'rechaz') !== false || $s === '2' || $s === '-1') {
            return ['label' => 'Desaprobado', 'class' => 'bg-red-600 text-white'];
        }
        // Cancelado
        if (strpos($s, 'cancel') !== false || $s === 'cancelado') {
            return ['label' => 'Cancelado', 'class' => 'bg-red-600 text-white'];
        }
        // Completado / terminado
        if (strpos($s, 'complet') !== false || $s === 'completado' || $s === 'finalizado' || $s === 'terminado') {
            return ['label' => 'Completado', 'class' => 'bg-green-500/20 text-green-500'];
        }
        // Activo / en curso
        if (strpos($s, 'activ') !== false || $s === 'activo' || $s === 'en curso') {
            return ['label' => 'Activo', 'class' => 'bg-primary/20 text-primary'];
        }
        // Aprobado
        if ($s === '1' || $s === 'true' || strpos($s, 'apro') !== false || $s === 'aprobado') {
            return ['label' => 'Aprobado', 'class' => 'bg-green-600 text-white'];
        }
        // Fallback: mostrar raw capitalizado y clase neutra
        $label = $raw ? ucfirst((string)$raw) : 'En espera';
        return ['label' => $label, 'class' => 'bg-primary/20 text-primary'];
    }
}
