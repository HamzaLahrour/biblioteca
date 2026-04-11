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
    public function crearPrestamo(array $datos, $usuarioLogueadoId) {}
    public function devolverPrestamo(Prestamo $prestamo): array
    {
        // TODO
    }

    public function renovarPrestamo(Prestamo $prestamo, $usuarioLogueadoId): Prestamo
    {
        // TODO
    }

    public function marcarComoPerdido(Prestamo $prestamo, $adminId): Prestamo
    {
        // TODO
    }

    // ═══════════════════════════════════════════
    // VALIDACIONES — USUARIO
    // ═══════════════════════════════════════════

    private function validarPermisoUsuario($userId, $logueadoId): void
    {
        // Usuario normal no puede pedir préstamo para otro

        if ($userId && $userId != $logueadoId) {
            $user = User::find($logueadoId);
            if ($user->rol !== 'admin') {
                throw new Exception('Acción denegada: No puedes pedir un prestamo en nombre de otra persona.');
            }
        }
    }

    private function validarUsuarioExiste($userId): void
    {
        // User::findOrFail

        $usuarioExiste = User::find($userId);
        if (!$usuarioExiste) {
            throw new Exception('El usuario no existe.');
        }
    }

    private function validarSanciones($userId): void
    {
        $fechaHoy = Carbon::today();
        $tieneSancion = Sancion::where('user_id', $userId)
            ->where('fecha_fin', '>=', $fechaHoy)->exists();


        if ($tieneSancion) {
            throw new Exception('Acción denegada: No puedes pedir un prestamo, por que tienes una sancion que aun no ha finalizado.');
        }
    }

    private function validarLimitePrestamos($userId): void
    {

        $maxPrestamosActivos = Configuracion::get('max_prestamos_activos');

        $prestamosActivos = Prestamo::where('user_id', $userId)
            ->where('estado', 'activo')->count();

        if ($prestamosActivos >= $maxPrestamosActivos) {
            throw new Exception("Acción denegada: Has alcanzado el limite de prestamos activos, el limite es de {$maxPrestamosActivos} prestamos activos.");
        }

        // Préstamos activos >= max_prestamos_activos
    }

    private function validarDeudaPendiente($userId): void
    {
        $entregaTarde = Prestamo::where('user_id', $userId)
            ->where('estado', 'devuelto_tarde')
            ->whereDoesntHave('sancion')
            ->exists();

        if ($entregaTarde) {
            throw new Exception('Tienes préstamos devueltos tarde pendientes de sanción. Contacta con el bibliotecario.');
        }
        // ¿Tiene préstamos devuelto_tarde sin sanción generada?
    }

    // ═══════════════════════════════════════════
    // VALIDACIONES — LIBRO
    // ═══════════════════════════════════════════

    private function validarLibroExiste($libroId): void
    {
        // Libro::findOrFail

        $libroExiste = Libro::findOrFail($libroId);
    }

    private function validarLibroDisponible($libroId): void
    {
        // stock_disponible > 0

        $libroNoDisponible = Libro::where('id', $libroId)
            ->where('copias_totales', '=', 0)->exists();

        if ($libroNoDisponible) {
            throw new Exception('Este libro actualmente no está disponible en este momento.');
        }
    }

    private function validarLibroNoEnReparacion($libroId): void
    {
        // estado !== 'en_reparacion'

        $libroNoEnReparacion = Libro::where('id', $libroId)
            ->where('estado', '!==', 'en_reparacion')->exists();

        if (!$libroNoEnReparacion) {
            throw new Exception('Este libro actualmente se encuentra en reparación.');
        }
    }

    private function validarLibroDisponibleTransaccion(Libro $libro): void
    {
        // Dentro del lockForUpdate comprobar stock_disponible > 0

        DB::transaction(function () use ($libro) {
            $libroBloqueado = Libro::where('id', $libro->id)
                ->lockForUpdate()->first();

            if (!$libroBloqueado || $libroBloqueado->copias_totales <= 0) {
                throw new Exception("No hay copias disponibles.");
            }
        });
    }

    // ═══════════════════════════════════════════
    // VALIDACIONES — FECHAS
    // ═══════════════════════════════════════════

    private function validarLogicaFechas($fechaInicio, $fechaFin): void
    {
        $inicio = Carbon::parse($fechaInicio)->startOfDay();
        $fin = Carbon::parse($fechaFin)->startOfDay();

        // 1. El inicio no puede ser en el pasado
        if ($inicio->isPast() && !$inicio->isToday()) {
            throw new Exception("Acción denegada: La fecha de inicio del préstamo no puede estar en el pasado.");
        }

        // 2. El fin no puede ser hoy ni en el pasado
        if ($fin->isPast() || $fin->isToday()) {
            throw new Exception("Acción denegada: La fecha de devolución debe ser al menos el día de mañana.");
        }

        // 3. El fin TIENE que ser posterior al inicio (Viajes en el tiempo prohibidos)
        if ($fin->lessThanOrEqualTo($inicio)) {
            throw new Exception("Acción denegada: La fecha de devolución debe ser posterior a la fecha de inicio.");
        }
    }

    private function validarDiasMinimos($fechaInicio, $fechaFin): void
    {
        $inicio = Carbon::parse($fechaInicio)->startOfDay();
        $fin = Carbon::parse($fechaFin)->startOfDay();

        $minimoDias = Configuracion::get('min_dias_prestamo', 1); // Valor por defecto 1 por seguridad

        if ($inicio->diffInDays($fin) < $minimoDias) {
            throw new Exception("Acción denegada: El préstamo debe ser de al menos {$minimoDias} día(s).");
        }
    }

    private function validarDiasMaximos($fechaInicio, $fechaFin): void
    {
        $inicio = Carbon::parse($fechaInicio)->startOfDay();
        $fin = Carbon::parse($fechaFin)->startOfDay();

        $maxDias = Configuracion::get('dias_prestamo', 15); // Valor por defecto 15 por seguridad

        if ($inicio->diffInDays($fin) > $maxDias) {
            throw new Exception("Acción denegada: El periodo de préstamo no puede superar los {$maxDias} días.");
        }
    }

    private function validarDiaApertura($fecha, $contexto = 'devolucion'): void
    {
        $configValor = Configuracion::where('clave', 'horario_semanal')->value('valor');

        if (!$configValor) {
            return;
        }

        $horario = is_string($configValor) ? json_decode($configValor, true) : $configValor;
        $fechaParseada = Carbon::parse($fecha);

        $mapaDias = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo'
        ];

        $nombreDia = $mapaDias[$fechaParseada->dayOfWeekIso];
        $estaAbierto = filter_var($horario[$nombreDia]['abierto'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (!$estaAbierto) {
            $accion = ($contexto === 'inicio') ? 'iniciar un préstamo' : 'devolver un libro';
            throw new Exception("Acción denegada: No puedes {$accion} un {$nombreDia}, la biblioteca permanece cerrada.");
        }
    }

    private function validarFechaDevolucionNoFestivo($fecha, $contexto = 'devolucion'): void
    {
        $festivo = Festivo::where('fecha', $fecha)->first();

        if ($festivo) {
            $accion = ($contexto === 'inicio') ? 'iniciar un préstamo' : 'devolver un libro';
            $motivo = $festivo->motivo ?? 'un día festivo';

            throw new Exception("Acción denegada: No puedes {$accion} el día " .
                Carbon::parse($fecha)->format('d/m') . " porque es {$motivo}.");
        }
    }

    private function validarRenovacionNoVencida(Prestamo $prestamo): void
    {
        $fechaTope = Carbon::parse($prestamo->fecha_devolucion_prevista)->startOfDay();

        if (Carbon::today()->greaterThan($fechaTope)) {
            throw new Exception("Acción denegada: Este préstamo ya está vencido. Debes devolver el libro en el mostrador.");
        }
    }
    private function validarExtensionReal(Prestamo $prestamo, $nuevaFechaFin): void
    {
        $fechaActual = Carbon::parse($prestamo->fecha_devolucion_prevista)->startOfDay();
        $nuevaFecha = Carbon::parse($nuevaFechaFin)->startOfDay();

        if ($nuevaFecha->lessThanOrEqualTo($fechaActual)) {
            throw new Exception("Acción denegada: La nueva fecha de devolución debe ser posterior a la actual ({$fechaActual->format('d/m/Y')}).");
        }
    }




    // ═══════════════════════════════════════════
    // VALIDACIONES — PRÉSTAMO
    // ═══════════════════════════════════════════

    private function validarPrestamoActivo(Prestamo $prestamo): void
    {
        // estado === 'activo'
        if ($prestamo->estado !== 'activo') {
            throw new Exception("Acción denegada: Este préstamo ya no se encuentra activo.");
        }
    }

    private function validarPrestamoNoYaDevuelto(Prestamo $prestamo): void
    {
        // fecha_devolucion_real === null

        if (!is_null($prestamo->fecha_devolucion_real)) {
            throw new Exception("Acción denegada: El libro ya consta como devuelto en el sistema.");
        }
    }



    private function validarLimiteRenovaciones(Prestamo $prestamo): void
    {
        // renovaciones < max_renovaciones
        $limite = Configuracion::get('max_renovaciones', 2);

        if ($prestamo->renovaciones >= $limite) {
            throw new Exception("Acción denegada: Has alcanzado el límite máximo de {$limite} renovaciones permitidas para este libro.");
        }
    }



    private function validarPermisoSobrePrestamo(Prestamo $prestamo, $logueadoId): void
    {
        // Solo el dueño o admin puede operar

        if ($prestamo->user_id == $logueadoId) {
            return;
        }
        $usuario = User::find($logueadoId);

        if ($usuario->rol !== 'admin') {
            throw new Exception("Acción denegada: Esta operación es exclusiva del personal de la biblioteca.");
        }
    }

    private function validarEsAdmin($adminId): void
    {
        // rol === 'admin'

        $usuario = User::find($adminId);

        if ($usuario->rol !== 'admin') {
            throw new Exception("Acción denegada: Esta operación es exclusiva del personal de la biblioteca.");
        }
    }

    // ═══════════════════════════════════════════
    // CÁLCULOS Y HELPERS
    // ═══════════════════════════════════════════

    private function calcularRetraso(Prestamo $prestamo): bool
    {
        // now() > fecha_devolucion_prevista + dias_gracia
    }

    private function calcularDiasRetraso(Prestamo $prestamo): int
    {
        // Diferencia entre hoy y fecha_devolucion_prevista
        // Si es negativo devuelve 0
    }

    private function generarSancion(Prestamo $prestamo, int $diasRetraso): Sancion
    {
        // Crea sanción en tabla sanciones
        // dias_sancion = diasRetraso * dias_sancion_por_dia
    }

    private function generarSancionPorPerdida(Prestamo $prestamo): Sancion
    {
        // Sanción más larga
        // Configuracion::get('dias_sancion_perdida', 30)
    }
}
