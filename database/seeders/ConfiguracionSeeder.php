<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Configuracion;


class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones =  [

            // ─── HORARIO ────────────────────────────────────────────
            [
                'seccion'     => 'horario',
                'clave'       => 'horario_semanal',
                'valor'       => json_encode([
                    'lunes'     => ['abierto' => false, 'apertura' => null,    'cierre' => null],
                    'martes'    => ['abierto' => true,  'apertura' => '09:00', 'cierre' => '21:00'],
                    'miercoles' => ['abierto' => true,  'apertura' => '09:00', 'cierre' => '21:00'],
                    'jueves'    => ['abierto' => true,  'apertura' => '09:00', 'cierre' => '21:00'],
                    'viernes'   => ['abierto' => true,  'apertura' => '09:00', 'cierre' => '21:00'],
                    'sabado'    => ['abierto' => true,  'apertura' => '10:00', 'cierre' => '14:00'],
                    'domingo'   => ['abierto' => false, 'apertura' => null,    'cierre' => null],
                ]),
                'tipo'        => 'json',
                'etiqueta'    => 'Horario de apertura semanal',
                'descripcion' => 'Configura qué días abre la biblioteca y en qué horario específico.',
            ],

            // ─── RESERVAS ───────────────────────────────────────────
            [
                'seccion'     => 'reservas',
                'clave'       => 'duracion_minima',
                'valor'       => '30',
                'tipo'        => 'integer',
                'etiqueta'    => 'Duración mínima (minutos)',
                'descripcion' => 'Tiempo mínimo que puede durar una reserva.',
            ],
            [
                'seccion'     => 'reservas',
                'clave'       => 'duracion_maxima',
                'valor'       => '180',
                'tipo'        => 'integer',
                'etiqueta'    => 'Duración máxima (minutos)',
                'descripcion' => 'Tiempo máximo que puede durar una reserva.',
            ],
            [
                'seccion'     => 'reservas',
                'clave'       => 'antelacion_minima',
                'valor'       => '30',
                'tipo'        => 'integer',
                'etiqueta'    => 'Antelación mínima (minutos)',
                'descripcion' => 'Con cuánta antelación mínima se puede reservar.',
            ],
            [
                'seccion'     => 'reservas',
                'clave'       => 'antelacion_maxima',
                'valor'       => '15',
                'tipo'        => 'integer',
                'etiqueta'    => 'Antelación máxima (días)',
                'descripcion' => 'Con cuántos días de antelación máxima se puede reservar.',
            ],
            [
                'seccion'     => 'reservas',
                'clave'       => 'max_reservas_activas',
                'valor'       => '2',
                'tipo'        => 'integer',
                'etiqueta'    => 'Máximo reservas activas por usuario',
                'descripcion' => 'Número máximo de reservas activas que puede tener un usuario.',
            ],
            [
                'seccion'     => 'reservas',
                'clave'       => 'max_horas_diarias',
                'valor'       => '360',
                'tipo'        => 'integer',
                'etiqueta'    => 'Máximo horas diarias (minutos)',
                'descripcion' => 'Total de minutos que un usuario puede reservar en un mismo día.',
            ],
            [
                'seccion'     => 'reservas',
                'clave'       => 'buffer_limpieza',
                'valor'       => '15',
                'tipo'        => 'integer',
                'etiqueta'    => 'Buffer de limpieza (minutos)',
                'descripcion' => 'Minutos de margen entre una reserva y la siguiente en el mismo espacio.',
            ],


            // ─── PRÉSTAMOS ──────────────────────────────────────────
            [
                'seccion'     => 'prestamos',
                'clave'       => 'dias_prestamo',
                'valor'       => '15',
                'tipo'        => 'integer',
                'etiqueta'    => 'Duración del préstamo (días)',
                'descripcion' => 'Número de días que dura un préstamo.',
            ],
            [
                'seccion'     => 'prestamos',
                'clave'       => 'max_prestamos_activos',
                'valor'       => '3',
                'tipo'        => 'integer',
                'etiqueta'    => 'Máximo préstamos activos por usuario',
                'descripcion' => 'Número máximo de préstamos activos simultáneos.',
            ],
            [
                'seccion'     => 'prestamos',
                'clave'       => 'min_dias_prestamo',
                'valor'       => '3',
                'tipo'        => 'integer',
                'etiqueta'    => 'Mínimo de días para solicitar un préstamo',
                'descripcion' => 'Número mínimo de días por los que un usuario puede solicitar un préstamo.',
            ],
            [
                'seccion'     => 'prestamos',
                'clave' => 'max_renovaciones',
                'valor'       => '2', // Límite por defecto (2 renovaciones suele ser el estándar)
                'tipo'        => 'numero',
                'etiqueta'    => 'Límite Máximo de Renovaciones',
                'descripcion' => 'Número máximo de veces que un alumno puede ampliar el tiempo de un mismo préstamo activo.',
            ],
            [
                'seccion'     => 'prestamos',
                'clave'       => 'dias_sancion_por_dia',
                'valor'       => '2',
                'tipo'        => 'integer',
                'etiqueta'    => 'Días de sanción por cada día de retraso',
                'descripcion' => 'Multiplicador de días de castigo por cada día de retraso.',
            ],
            [
                'seccion'     => 'prestamos',
                'clave'       => 'dias_sancion_perdida',
                'valor'       => '30',
                'tipo'        => 'integer',
                'etiqueta'    => 'Días de sanción por libro perdido',
                'descripcion' => 'Días de sanción cuando un usuario pierde un libro.',
            ],

            // ─── SANCIONES ──────────────────────────────────────────
            [
                'seccion'     => 'sanciones',
                'clave'       => 'dias_sancion',
                'valor'       => '7',
                'tipo'        => 'integer',
                'etiqueta'    => 'Días de sanción por retraso',
                'descripcion' => 'Días que dura una sanción generada por devolver tarde.',
            ],
            [
                'seccion'     => 'sanciones',
                'clave'       => 'sanciones_bloquean_reservas',
                'valor'       => 'true',
                'tipo'        => 'boolean',
                'etiqueta'    => 'Las sanciones bloquean reservas',
                'descripcion' => 'Si está activo, un usuario sancionado no puede hacer reservas.',
            ],
            [
                'seccion'     => 'sanciones',
                'clave'       => 'sanciones_bloquean_prestamos',
                'valor'       => 'true',
                'tipo'        => 'boolean',
                'etiqueta'    => 'Las sanciones bloquean préstamos',
                'descripcion' => 'Si está activo, un usuario sancionado no puede pedir préstamos.',
            ],
        ];

        foreach ($configuraciones as $config) {
            Configuracion::updateOrCreate(
                ['clave' => $config['clave']], // busca por clave
                $config                         // si no existe lo crea, si existe lo actualiza
            );
        }
    }
}
