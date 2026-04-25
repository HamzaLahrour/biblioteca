<?php

namespace App\Services;



use App\Models\Reserva;
use App\Models\Espacio;
use App\Models\Festivo;
use App\Models\Configuracion;
use App\Models\Configuracion as ModelsConfiguracion;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\User;
use App\Models\Sancion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;


class PrestamoService
{
    /**
     * Create a new class instance.
     */
    public function crearPrestamo(array $datos, $usuarioLogueadoId): Prestamo
    {
        $userId      = $datos['user_id'] ?? $usuarioLogueadoId;
        $fechaInicio = now();

        $this->validarPermisoUsuario($userId, $usuarioLogueadoId);
        $this->validarUsuarioExiste($userId);
        $this->validarSanciones($userId);
        $this->validarLimitePrestamos($userId);
        $this->validarDeudaPendiente($userId);
        $this->validarLibroExiste($datos['libro_id']);
        $this->validarLibroDisponible($datos['libro_id']);
        $this->validarLibroNoEnReparacion($datos['libro_id']);
        $this->validarNoHaySolicitudMismoLibro($datos['libro_id'], $userId);
        $this->validarLogicaFechas($fechaInicio, $datos['fecha_devolucion_prevista']);
        $this->validarDiasMinimos($fechaInicio, $datos['fecha_devolucion_prevista']);
        $this->validarDiasMaximos($fechaInicio, $datos['fecha_devolucion_prevista']);
        $this->validarDiaApertura($fechaInicio, 'inicio');
        $this->validarDiaApertura($datos['fecha_devolucion_prevista'], 'devolucion');
        $this->validarFechaDevolucionNoFestivo($datos['fecha_devolucion_prevista'], 'devolver un libro');

        $yaLoTiene = Prestamo::where('user_id', $userId)
            ->where('libro_id', $datos['libro_id'])
            ->where('estado', 'activo')->exists();
        if ($yaLoTiene) {
            throw new Exception("El usuario ya tiene una copia activa de este mismo libro.");
        }

        return DB::transaction(function () use ($datos, $userId, $fechaInicio) {
            $libro = Libro::lockForUpdate()->findOrFail($datos['libro_id']);
            $this->validarLibroDisponibleTransaccion($libro);

            return Prestamo::create([
                'user_id'                  => $userId,
                'libro_id'                 => $libro->id,
                'fecha_prestamo'           => $fechaInicio,
                'fecha_devolucion_prevista' => $datos['fecha_devolucion_prevista'],
                'estado'                   => 'activo',
                'renovaciones'             => 0,
            ]);
        });
    }

    public function devolverPrestamo(Prestamo $prestamo): array
    {
        $this->validarPrestamoActivo($prestamo);
        $this->validarPrestamoNoYaDevuelto($prestamo);

        return DB::transaction(function () use ($prestamo) {
            $esTarde     = $this->calcularRetraso($prestamo);
            $diasRetraso = $this->calcularDiasRetraso($prestamo);

            $prestamo->update([
                'fecha_devolucion_real' => now(),
                'estado'                => $esTarde ? 'devuelto_tarde' : 'devuelto',
                'dias_retraso'          => $diasRetraso,
            ]);

            $sancion = null;
            if ($esTarde) {
                $sancion = $this->generarSancion($prestamo, $diasRetraso);
            }

            return [
                'prestamo'     => $prestamo,
                'tarde'        => $esTarde,
                'dias_retraso' => $diasRetraso,
                'sancion'      => $sancion,
            ];
        });
    }

    public function renovarPrestamo(Prestamo $prestamo, $usuarioLogueadoId): Prestamo
    {
        $this->validarPermisoSobrePrestamo($prestamo, $usuarioLogueadoId);
        $this->validarPrestamoActivo($prestamo);
        $this->validarPuedeRenovar($prestamo);
        $this->validarLimiteRenovaciones($prestamo);
        $this->validarSanciones($prestamo->user_id);
        $this->validarNoHaySolicitudMismoLibro($prestamo->libro_id, $prestamo->user_id);

        $diasPrestamo         = Configuracion::get('dias_prestamo', 15);
        $nuevaFechaDevolucion = Carbon::parse($prestamo->fecha_devolucion_prevista)->addDays($diasPrestamo);

        $this->validarExtensionReal($prestamo, $nuevaFechaDevolucion);
        $this->validarDiaApertura($nuevaFechaDevolucion, 'devolucion');
        $this->validarFechaDevolucionNoFestivo($nuevaFechaDevolucion, 'renovar un préstamo');

        $prestamo->update([
            'fecha_devolucion_prevista' => $nuevaFechaDevolucion,
            'renovaciones'              => $prestamo->renovaciones + 1,
        ]);

        return $prestamo->fresh();
    }

    public function marcarComoPerdido(Prestamo $prestamo, $adminId): Prestamo
    {
        $this->validarEsAdmin($adminId);
        $this->validarPrestamoActivo($prestamo);

        return DB::transaction(function () use ($prestamo) {
            $prestamo->update([
                'estado'                => 'perdido',
                'fecha_devolucion_real' => now(),
            ]);

            $this->generarSancionPorPerdida($prestamo);

            $libro = $prestamo->libro;

            if ($libro && $libro->copias_totales > 0) {
                $libro->decrement('copias_totales');
            }

            return $prestamo;
        });
    }

    // ═══════════════════════════════════════════
    // VALIDACIONES — USUARIO
    // ═══════════════════════════════════════════

    private function validarPermisoUsuario($userId, $logueadoId): void
    {
        if ($userId && $userId != $logueadoId) {
            $user = User::find($logueadoId);
            if ($user->rol !== 'admin') {
                throw new Exception('Acción denegada: No puedes pedir un préstamo en nombre de otra persona.');
            }
        }
    }

    private function validarUsuarioExiste($userId): void
    {
        if (!User::find($userId)) {
            throw new Exception('El usuario no existe.');
        }
    }

    private function validarSanciones($userId): void
    {
        $tieneSancion = Sancion::where('user_id', $userId)
            ->where('fecha_fin', '>=', Carbon::today())
            ->exists();

        if ($tieneSancion) {
            throw new Exception('Acción denegada: Tienes una sanción activa. No puedes pedir préstamos.');
        }
    }

    private function validarLimitePrestamos($userId): void
    {
        $maxPrestamosActivos = Configuracion::get('max_prestamos_activos', 3);
        $prestamosActivos    = Prestamo::where('user_id', $userId)
            ->where('estado', 'activo')
            ->count();

        if ($prestamosActivos >= $maxPrestamosActivos) {
            throw new Exception("Acción denegada: Has alcanzado el límite de {$maxPrestamosActivos} préstamos activos.");
        }
    }

    private function validarDeudaPendiente($userId): void
    {
        $tieneDeuda = Prestamo::where('user_id', $userId)
            ->where('estado', 'devuelto_tarde')
            ->whereDoesntHave('sancion')
            ->exists();

        if ($tieneDeuda) {
            throw new Exception('Tienes préstamos devueltos tarde pendientes de sanción. Contacta con el bibliotecario.');
        }
    }

    // ═══════════════════════════════════════════
    // VALIDACIONES — LIBRO
    // ═══════════════════════════════════════════

    private function validarLibroExiste($libroId): void
    {
        Libro::findOrFail($libroId);
    }

    private function validarLibroDisponible($libroId): void
    {
        $libro            = Libro::find($libroId);
        $prestamosActivos = Prestamo::where('libro_id', $libroId)
            ->where('estado', 'activo')
            ->count();

        if ($prestamosActivos >= $libro->copias_totales) {
            throw new Exception('Este libro no tiene copias disponibles en este momento.');
        }
    }

    private function validarLibroNoEnReparacion($libroId): void
    {
        $libro = Libro::find($libroId);
        if ($libro->estado === 'en_reparacion') {
            throw new Exception('Este libro está en reparación y no puede prestarse.');
        }
    }

    private function validarNoHaySolicitudMismoLibro($libroId, $userId = null): void
    {
        // TODO v2.0: bloquear si hay lista de espera
    }

    private function validarLibroDisponibleTransaccion(Libro $libro): void
    {
        $prestamosActivos = Prestamo::where('libro_id', $libro->id)
            ->where('estado', 'activo')
            ->count();

        if ($prestamosActivos >= $libro->copias_totales) {
            throw new Exception('No hay copias disponibles. Alguien se lo acaba de llevar.');
        }
    }

    // ═══════════════════════════════════════════
    // VALIDACIONES — FECHAS
    // ═══════════════════════════════════════════

    private function validarLogicaFechas($fechaInicio, $fechaFin): void
    {
        $inicio = Carbon::parse($fechaInicio)->startOfDay();
        $fin    = Carbon::parse($fechaFin)->startOfDay();

        if ($fin->isPast() || $fin->isToday()) {
            throw new Exception('Acción denegada: La fecha de devolución debe ser al menos mañana.');
        }

        if ($fin->lessThanOrEqualTo($inicio)) {
            throw new Exception('Acción denegada: La fecha de devolución debe ser posterior al inicio.');
        }
    }

    private function validarDiasMinimos($fechaInicio, $fechaFin): void
    {
        $inicio     = Carbon::parse($fechaInicio)->startOfDay();
        $fin        = Carbon::parse($fechaFin)->startOfDay();
        $minimoDias = Configuracion::get('min_dias_prestamo', 1);

        if ($inicio->diffInDays($fin) < $minimoDias) {
            throw new Exception("Acción denegada: El préstamo debe ser de al menos {$minimoDias} día(s).");
        }
    }

    private function validarDiasMaximos($fechaInicio, $fechaFin): void
    {
        $inicio  = Carbon::parse($fechaInicio)->startOfDay();
        $fin     = Carbon::parse($fechaFin)->startOfDay();
        $maxDias = Configuracion::get('dias_prestamo', 15);

        if ($inicio->diffInDays($fin) > $maxDias) {
            throw new Exception("Acción denegada: El periodo no puede superar los {$maxDias} días.");
        }
    }

    private function validarDiaApertura($fecha, $contexto = 'devolucion'): void
    {
        $configValor = Configuracion::where('clave', 'dias_apertura')->value('valor');

        if (!$configValor) return;

        $diasAbiertos  = array_map('trim', explode(',', $configValor));
        $fechaParseada = Carbon::parse($fecha);

        $mapaDias = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo',
        ];

        $nombreDia   = $mapaDias[$fechaParseada->dayOfWeekIso];
        $estaAbierto = in_array($nombreDia, $diasAbiertos);

        if (!$estaAbierto) {
            $accion = $contexto === 'inicio' ? 'iniciar un préstamo' : 'devolver un libro';
            throw new Exception("Acción denegada: No puedes {$accion} un {$nombreDia}, la biblioteca cierra.");
        }
    }

    private function validarFechaDevolucionNoFestivo($fecha, $contexto = 'devolucion'): void
    {
        $festivo = Festivo::where('fecha', Carbon::parse($fecha)->toDateString())->first();

        if ($festivo) {
            $motivo = $festivo->motivo ?? 'un día festivo';
            throw new Exception("Acción denegada: No puedes {$contexto} el día " .
                Carbon::parse($fecha)->format('d/m') . " porque es {$motivo}.");
        }
    }

    private function validarExtensionReal(Prestamo $prestamo, $nuevaFechaFin): void
    {
        $fechaActual = Carbon::parse($prestamo->fecha_devolucion_prevista)->startOfDay();
        $nuevaFecha  = Carbon::parse($nuevaFechaFin)->startOfDay();

        if ($nuevaFecha->lessThanOrEqualTo($fechaActual)) {
            throw new Exception("Acción denegada: La nueva fecha debe ser posterior a la actual ({$fechaActual->format('d/m/Y')}).");
        }
    }

    // ═══════════════════════════════════════════
    // VALIDACIONES — PRÉSTAMO
    // ═══════════════════════════════════════════

    private function validarPrestamoActivo(Prestamo $prestamo): void
    {
        if ($prestamo->estado !== 'activo') {
            throw new Exception('Acción denegada: Este préstamo ya no se encuentra activo.');
        }
    }

    private function validarPrestamoNoYaDevuelto(Prestamo $prestamo): void
    {
        if (!is_null($prestamo->fecha_devolucion_real)) {
            throw new Exception('Acción denegada: El libro ya consta como devuelto en el sistema.');
        }
    }

    private function validarPuedeRenovar(Prestamo $prestamo): void
    {
        if (Carbon::today()->gt(Carbon::parse($prestamo->fecha_devolucion_prevista))) {
            throw new Exception('Acción denegada: No puedes renovar un préstamo que ya ha vencido. Debes devolver el libro.');
        }
    }

    private function validarLimiteRenovaciones(Prestamo $prestamo): void
    {
        $limite = Configuracion::get('max_renovaciones', 2);

        if ($prestamo->renovaciones >= $limite) {
            throw new Exception("Acción denegada: Has alcanzado el límite de {$limite} renovaciones.");
        }
    }

    private function validarPermisoSobrePrestamo(Prestamo $prestamo, $logueadoId): void
    {
        if ($prestamo->user_id == $logueadoId) return;

        $usuario = User::find($logueadoId);
        if ($usuario->rol !== 'admin') {
            throw new Exception('Acción denegada: Esta operación es exclusiva del personal de la biblioteca.');
        }
    }

    private function validarEsAdmin($adminId): void
    {
        $usuario = User::find($adminId);
        if ($usuario->rol !== 'admin') {
            throw new Exception('Acción denegada: Esta operación es exclusiva del personal de la biblioteca.');
        }
    }

    // ═══════════════════════════════════════════
    // CÁLCULOS Y HELPERS
    // ═══════════════════════════════════════════

    private function calcularRetraso(Prestamo $prestamo): bool
    {
        $fechaPrevista = Carbon::parse($prestamo->fecha_devolucion_prevista)->startOfDay();
        return Carbon::today()->greaterThan($fechaPrevista);
    }

    private function calcularDiasRetraso(Prestamo $prestamo): int
    {
        $fechaPrevista = Carbon::parse($prestamo->fecha_devolucion_prevista)->startOfDay();
        $hoy           = Carbon::today();

        if ($hoy->lessThanOrEqualTo($fechaPrevista)) {
            return 0;
        }

        return $fechaPrevista->diffInDays($hoy);
    }

    private function crearSancion(Prestamo $prestamo, int $diasCastigo, string $motivo): Sancion
    {
        $sancionActiva = Sancion::where('user_id', $prestamo->user_id)
            ->where('fecha_fin', '>', Carbon::today())
            ->orderBy('fecha_fin', 'desc')
            ->first();

        $fechaInicio = $sancionActiva
            ? Carbon::parse($sancionActiva->fecha_fin)
            : Carbon::today();

        $fechaFin = $fechaInicio->copy()->addDays($diasCastigo);

        return Sancion::create([
            'user_id'      => $prestamo->user_id,
            'prestamo_id'  => $prestamo->id,
            'razon'        => $motivo,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin,
        ]);
    }

    private function generarSancion(Prestamo $prestamo, int $diasRetraso): Sancion
    {
        $multiplicador = Configuracion::get('dias_sancion_por_dia', 2);
        return $this->crearSancion(
            $prestamo,
            $diasRetraso * $multiplicador,
            "Retraso de {$diasRetraso} días en la devolución del libro."
        );
    }

    private function generarSancionPorPerdida(Prestamo $prestamo): Sancion
    {
        $diasCastigo = Configuracion::get('dias_sancion_perdida', 30);
        return $this->crearSancion(
            $prestamo,
            $diasCastigo,
            "Pérdida del material prestado."
        );
    }
}
