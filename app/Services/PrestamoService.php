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
    // ═══════════════════════════════════════════
    // MÉTODOS PRINCIPALES
    // ═══════════════════════════════════════════

    /**
     * Gestiona el proceso completo de creación de un préstamo. 
     * Ejecuta una serie de validaciones previas para garantizar la integridad de los datos 
     * y el cumplimiento de las reglas de negocio antes de la persistencia.
     */
    public function crearPrestamo(array $datos, $usuarioLogueadoId): Prestamo
    {
        // Si no se proporciona un ID de usuario en los datos, se asume el ID del usuario autenticado en el sistema.
        $userId      = $datos['user_id'] ?? $usuarioLogueadoId;
        $fechaInicio = now();

        // Bloque de validaciones de reglas de negocio. Si alguna condición no se cumple, se lanzará una excepción interrumpiendo el flujo.
        $this->validarPermisoUsuario($userId, $usuarioLogueadoId);
        $this->validarUsuarioExiste($userId);
        $this->validarSanciones($userId);
        $this->validarLimitePrestamos($userId);
        $this->validarDeudaPendiente($userId);
        $this->validarLibroExiste($datos['libro_id']);
        $this->validarLibroDisponible($datos['libro_id']);
        $this->validarLibroNoEnReparacion($datos['libro_id']);
        $this->validarNoHaySolicitudMismoLibro($datos['libro_id'], $userId);

        // Verificación de la coherencia de las fechas y disponibilidad del calendario de la biblioteca.
        $this->validarLogicaFechas($fechaInicio, $datos['fecha_devolucion_prevista']);
        $this->validarDiasMinimos($fechaInicio, $datos['fecha_devolucion_prevista']);
        $this->validarDiasMaximos($fechaInicio, $datos['fecha_devolucion_prevista']);
        $this->validarDiaApertura($fechaInicio, 'inicio');
        $this->validarDiaApertura($datos['fecha_devolucion_prevista'], 'devolucion');
        $this->validarFechaDevolucionNoFestivo($datos['fecha_devolucion_prevista'], 'devolver un libro');

        // Evita la duplicidad: verifica que el usuario no disponga ya de una copia activa del mismo título.
        $yaLoTiene = Prestamo::where('user_id', $userId)
            ->where('libro_id', $datos['libro_id'])
            ->where('estado', 'activo')->exists();

        if ($yaLoTiene) {
            throw new Exception("El usuario ya tiene una copia activa de este mismo libro.");
        }

        // Se utiliza una transacción de base de datos para asegurar la atomicidad de la operación y evitar inconsistencias en caso de error.
        return DB::transaction(function () use ($datos, $userId, $fechaInicio) {
            // El bloqueo pesimista (lockForUpdate) previene condiciones de carrera si múltiples usuarios intentan acceder a la última copia simultáneamente.
            $libro = Libro::lockForUpdate()->findOrFail($datos['libro_id']);

            // Re-evaluación de la disponibilidad tras adquirir el bloqueo para garantizar que el estado del inventario no haya cambiado.
            $this->validarLibroDisponibleTransaccion($libro);

            return Prestamo::create([
                'user_id'                   => $userId,
                'libro_id'                  => $libro->id,
                'fecha_prestamo'           => $fechaInicio,
                'fecha_devolucion_prevista' => $datos['fecha_devolucion_prevista'],
                'estado'                   => 'activo',
                'renovaciones'             => 0,
            ]);
        });
    }

    /**
     * Gestiona el proceso de devolución de un préstamo.
     * Evalúa posibles retrasos en la entrega y genera las sanciones correspondientes si aplica.
     */
    public function devolverPrestamo(Prestamo $prestamo): array
    {
        $this->validarPrestamoActivo($prestamo);
        $this->validarPrestamoNoYaDevuelto($prestamo);

        return DB::transaction(function () use ($prestamo) {
            // Cálculo de penalizaciones: determina si existe retraso y la cantidad de días de demora.
            $esTarde     = $this->calcularRetraso($prestamo);
            $diasRetraso = $this->calcularDiasRetraso($prestamo);

            // Actualización del registro del préstamo con la fecha real de devolución y su estado final.
            $prestamo->update([
                'fecha_devolucion_real' => now(),
                'estado'                => $esTarde ? 'devuelto_tarde' : 'devuelto',
                'dias_retraso'          => $diasRetraso,
            ]);

            $sancion = null;
            // Generación de sanción automática en caso de devolución fuera del plazo establecido.
            if ($esTarde) {
                $sancion = $this->generarSancion($prestamo, $diasRetraso);
            }

            // Retorna un arreglo estructurado con la información detallada del proceso para ser procesado por el controlador.
            return [
                'prestamo'     => $prestamo,
                'tarde'        => $esTarde,
                'dias_retraso' => $diasRetraso,
                'sancion'      => $sancion,
            ];
        });
    }

    /**
     * Gestiona la extensión (renovación) de la fecha de devolución de un préstamo activo.
     */
    public function renovarPrestamo(Prestamo $prestamo, $usuarioLogueadoId): Prestamo
    {
        // Verifica que el usuario solicitante sea el titular del préstamo o posea rol de administrador.
        $this->validarPermisoSobrePrestamo($prestamo, $usuarioLogueadoId);
        $this->validarPrestamoActivo($prestamo);
        $this->validarPuedeRenovar($prestamo);
        $this->validarLimiteRenovaciones($prestamo);
        $this->validarSanciones($prestamo->user_id);
        $this->validarNoHaySolicitudMismoLibro($prestamo->libro_id, $prestamo->user_id);

        // Obtiene los días de extensión permitidos desde la configuración del sistema (valor por defecto: 15 días).
        $diasPrestamo         = Configuracion::get('dias_prestamo', 15);
        $nuevaFechaDevolucion = Carbon::parse($prestamo->fecha_devolucion_prevista)->addDays($diasPrestamo);

        // Valida que la nueva fecha propuesta de devolución sea coherente y recaiga en un día operativo de la institución.
        $this->validarExtensionReal($prestamo, $nuevaFechaDevolucion);
        $this->validarDiaApertura($nuevaFechaDevolucion, 'devolucion');
        $this->validarFechaDevolucionNoFestivo($nuevaFechaDevolucion, 'renovar un préstamo');

        // Actualiza la fecha límite e incrementa el contador histórico de renovaciones del préstamo.
        $prestamo->update([
            'fecha_devolucion_prevista' => $nuevaFechaDevolucion,
            'renovaciones'              => $prestamo->renovaciones + 1,
        ]);

        return $prestamo->fresh();
    }

    /**
     * Registra la pérdida definitiva de un material prestado. 
     * Operación estrictamente restringida a usuarios con rol de administrador.
     */
    public function marcarComoPerdido(Prestamo $prestamo, $adminId): Prestamo
    {
        $this->validarEsAdmin($adminId);
        $this->validarPrestamoActivo($prestamo);

        return DB::transaction(function () use ($prestamo) {
            // Actualiza el estado del préstamo a 'perdido' y establece la fecha actual como cierre definitivo.
            $prestamo->update([
                'estado'                => 'perdido',
                'fecha_devolucion_real' => now(),
            ]);

            // Genera la sanción administrativa correspondiente por el extravío del material.
            $this->generarSancionPorPerdida($prestamo);

            // Descuenta permanentemente una copia física del inventario general de dicho libro.
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

    // Verifica que el usuario no esté operando bajo el identificador de un tercero, exceptuando autorizaciones administrativas.
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

    // Verifica la existencia de sanciones activas que inhabiliten al usuario para solicitar nuevos préstamos.
    private function validarSanciones($userId): void
    {
        $tieneSancion = Sancion::where('user_id', $userId)
            ->where('fecha_fin', '>=', Carbon::today())
            ->exists();

        if ($tieneSancion) {
            throw new Exception('Acción denegada: Tienes una sanción activa. No puedes pedir préstamos.');
        }
    }

    // Comprueba que el usuario no haya excedido el límite máximo de préstamos activos simultáneos permitido por la institución.
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

    // Impide el registro de nuevos préstamos si el usuario posee devoluciones tardías pendientes de asignación de sanción.
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

    // Verifica que existan copias físicas disponibles comparando el total del inventario general con los préstamos activos.
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
        // TODO: Implementar bloqueo de solicitud en caso de existencia de listas de espera activas.
    }

    // Segunda capa de validación de disponibilidad exclusiva para ejecución dentro del contexto de bloqueo de transacción (lockForUpdate).
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

    // Valida la coherencia cronológica: la fecha de devolución debe ser estrictamente posterior a la fecha actual y de inicio.
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

    // Garantiza que el periodo del préstamo cumpla con la cantidad mínima de días estipulada en la configuración del sistema.
    private function validarDiasMinimos($fechaInicio, $fechaFin): void
    {
        $inicio     = Carbon::parse($fechaInicio)->startOfDay();
        $fin        = Carbon::parse($fechaFin)->startOfDay();
        $minimoDias = Configuracion::get('min_dias_prestamo', 1);

        if ($inicio->diffInDays($fin) < $minimoDias) {
            throw new Exception("Acción denegada: El préstamo debe ser de al menos {$minimoDias} día(s).");
        }
    }

    // Garantiza que el periodo total del préstamo no exceda el límite máximo de días permitido por la normativa.
    private function validarDiasMaximos($fechaInicio, $fechaFin): void
    {
        $inicio  = Carbon::parse($fechaInicio)->startOfDay();
        $fin     = Carbon::parse($fechaFin)->startOfDay();
        $maxDias = Configuracion::get('dias_prestamo', 15);

        if ($inicio->diffInDays($fin) > $maxDias) {
            throw new Exception("Acción denegada: El periodo no puede superar los {$maxDias} días.");
        }
    }

    // Asegura que las fechas clave operativas (inicio o fin del préstamo) coincidan con los días hábiles de apertura de la biblioteca.
    private function validarDiaApertura($fecha, $contexto = 'devolucion'): void
    {
        $configValor = Configuracion::where('clave', 'dias_apertura')->value('valor');

        if (!$configValor) return;

        $diasAbiertos  = array_map('trim', explode(',', $configValor));
        $fechaParseada = Carbon::parse($fecha);

        //Mapeo de dias 
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

    // Verifica la fecha propuesta contra la base de datos de festivos locales o nacionales para prevenir asignaciones en días no laborables.
    private function validarFechaDevolucionNoFestivo($fecha, $contexto = 'devolucion'): void
    {
        $festivo = Festivo::where('fecha', Carbon::parse($fecha)->toDateString())->first();

        if ($festivo) {
            $motivo = $festivo->motivo ?? 'un día festivo';
            throw new Exception("Acción denegada: No puedes {$contexto} el día " .
                Carbon::parse($fecha)->format('d/m') . " porque es {$motivo}.");
        }
    }

    // Asegura que la fecha propuesta para la renovación represente un incremento cronológico real respecto al plazo límite vigente.
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

    // Valida que el estado actual del préstamo permita la ejecución de operaciones de modificación (estado 'activo').
    private function validarPrestamoActivo(Prestamo $prestamo): void
    {
        if ($prestamo->estado !== 'activo') {
            throw new Exception('Acción denegada: Este préstamo ya no se encuentra activo.');
        }
    }

    // Previene la potencial doble ejecución de un proceso de devolución verificando la ausencia previa de una fecha de devolución real.
    private function validarPrestamoNoYaDevuelto(Prestamo $prestamo): void
    {
        if (!is_null($prestamo->fecha_devolucion_real)) {
            throw new Exception('Acción denegada: El libro ya consta como devuelto en el sistema.');
        }
    }

    // Evalúa que se cumplan las condiciones cronológicas y de vigencia estipuladas para autorizar la renovación de un préstamo.
    private function validarPuedeRenovar(Prestamo $prestamo): void
    {
        $hoy = Carbon::today();
        $vence = Carbon::parse($prestamo->fecha_devolucion_prevista)->startOfDay();

        // Bloquea cualquier intento de renovación si el préstamo ya ha expirado y se encuentra en estado de mora.
        if (Carbon::today()->gt(Carbon::parse($prestamo->fecha_devolucion_prevista))) {
            throw new Exception('Acción denegada: No puedes renovar un préstamo que ya ha vencido. Debes devolver el libro.');
        }

        $diasRestantes = $hoy->diffInDays($vence, false);

        // Restringe el derecho a renovación exclusivamente a los días previos inmediatos al vencimiento (ventana de 3 días) para optimizar la circulación del catálogo.
        if ($diasRestantes > 3) {
            throw new Exception("Acción denegada: Solo puedes renovar el libro cuando queden 3 días o menos para la fecha límite.");
        }
    }

    // Verifica que la solicitud no sobrepase la cantidad máxima de renovaciones permitidas por el reglamento de la institución.
    private function validarLimiteRenovaciones(Prestamo $prestamo): void
    {
        $limite = Configuracion::get('max_renovaciones', 2);

        if ($prestamo->renovaciones >= $limite) {
            throw new Exception("Acción denegada: Has alcanzado el límite de {$limite} renovaciones.");
        }
    }

    // Garantiza estrictamente que la alteración de un préstamo solo pueda ser ejecutada por el titular del mismo o por un administrador del sistema.
    private function validarPermisoSobrePrestamo(Prestamo $prestamo, $logueadoId): void
    {
        if ($prestamo->user_id == $logueadoId) return;

        $usuario = User::find($logueadoId);
        if ($usuario->rol !== 'admin') {
            throw new Exception('Acción denegada: Esta operación es exclusiva del personal de la biblioteca.');
        }
    }

    // Restricción de acceso a nivel de método: asegura que el usuario solicitante posea los privilegios de administrador requeridos.
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

    // Determina de manera booleana si la fecha operativa actual ha superado la fecha máxima de devolución estipulada.
    private function calcularRetraso(Prestamo $prestamo): bool
    {
        $fechaPrevista = Carbon::parse($prestamo->fecha_devolucion_prevista)->startOfDay();
        return Carbon::today()->greaterThan($fechaPrevista);
    }

    // Calcula y retorna la cantidad exacta de días de demora, tomando como referencia la fecha límite acordada.
    private function calcularDiasRetraso(Prestamo $prestamo): int
    {
        $fechaPrevista = Carbon::parse($prestamo->fecha_devolucion_prevista)->startOfDay();
        $hoy           = Carbon::today();

        if ($hoy->lessThanOrEqualTo($fechaPrevista)) {
            return 0; // Se retorna 0 si la devolución se encuentra dentro del plazo.
        }

        return $fechaPrevista->diffInDays($hoy);
    }

    /**
     * Método central para la persistencia de registros sancionadores.
     * Implementa una lógica de acumulación de condenas: en caso de existir una sanción previa en curso, 
     * el cómputo de la nueva penalización iniciará a partir de la finalización de la anterior, evitando solapamientos.
     */
    private function crearSancion(Prestamo $prestamo, int $diasCastigo, string $motivo): Sancion
    {
        $sancionActiva = Sancion::where('user_id', $prestamo->user_id)
            ->where('fecha_fin', '>', Carbon::today())
            ->orderBy('fecha_fin', 'desc')
            ->first();

        // Determinación de la fecha de inicio en base al historial penal vigente del usuario.
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

    // Calcula y genera el registro de sanción correspondiente a la demora, aplicando el factor multiplicador definido en la parametrización institucional.
    private function generarSancion(Prestamo $prestamo, int $diasRetraso): Sancion
    {
        $multiplicador = Configuracion::get('dias_sancion_por_dia', 2);
        return $this->crearSancion(
            $prestamo,
            $diasRetraso * $multiplicador,
            "Retraso de {$diasRetraso} días en la devolución del libro."
        );
    }

    // Automatiza la generación y asignación de la sanción administrativa prevista por normativa interna para los casos de extravío definitivo del material.
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
