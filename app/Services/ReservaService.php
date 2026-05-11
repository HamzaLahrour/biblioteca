<?php

namespace App\Services;

use App\Models\Reserva;
use App\Models\Espacio;
use App\Models\Festivo;
use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class ReservaService
{
    /**
     * Punto de entrada principal para crear una reserva.
     * Pasa por todas las validaciones en orden antes de guardar nada en la BD.
     */
    public function crearReserva(array $datos, $usuarioLogueadoId)
    {
        $userId = $datos['user_id'] ?? $usuarioLogueadoId;

        $this->validarPermisoUsuario($userId, $usuarioLogueadoId);
        $this->validarHoraNoPasada($datos['fecha'], $datos['hora_inicio']);
        $this->validarAntelacionMinima($datos['fecha'], $datos['hora_inicio']);
        $this->validarAntelacionMaxima($datos['fecha']);
        $this->validarDuracion($datos['hora_inicio'], $datos['hora_fin']);
        $this->validarSanciones($userId);
        $this->validarLimiteReservas($userId);
        $this->validarHorasDiarias($userId, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
        $this->validarSolapamientoUsuario($userId, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
        $this->validarEspacioActivoYCapacidad($datos['espacio_id']);

        $this->validarHorarioSemanalJSON($datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
        $this->validarFestivo($datos['fecha']);

        // Todo limpio, bloqueamos el espacio y creamos la reserva dentro de una transacción
        return DB::transaction(function () use ($datos, $userId) {

            $espacio = Espacio::lockForUpdate()->findOrFail($datos['espacio_id']);

            $this->validarBufferLimpieza($espacio->id, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
            $this->validarDisponibilidadReal($espacio, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);

            return Reserva::create([
                'user_id'       => $userId,
                'espacio_id'    => $espacio->id,
                'fecha_reserva' => $datos['fecha'], // El formulario manda 'fecha', se guarda en 'fecha_reserva'
                'hora_inicio'   => $datos['hora_inicio'],
                'hora_fin'      => $datos['hora_fin'],
                'estado'        => 'activa',
            ]);
        });
    }

    /**
     * Cuando el usuario no elige espacio concreto, buscamos uno libre automáticamente.
     * Primero valida todas las reglas del usuario y luego va probando espacios uno a uno
     * hasta encontrar alguno que no choque con nada.
     */
    public function buscarEspacioDisponible($tipoEspacioId, array $datos, $usuarioLogueadoId)
    {
        $userId = $datos['user_id'] ?? $usuarioLogueadoId;

        // 1. REGLAS GENERALES (Filtramos al usuario y la fecha antes de mirar sillas)
        $this->validarPermisoUsuario($userId, $usuarioLogueadoId);
        $this->validarHoraNoPasada($datos['fecha'], $datos['hora_inicio']);
        $this->validarAntelacionMinima($datos['fecha'], $datos['hora_inicio']);
        $this->validarAntelacionMaxima($datos['fecha']);
        $this->validarDuracion($datos['hora_inicio'], $datos['hora_fin']);
        $this->validarSanciones($userId);
        $this->validarLimiteReservas($userId);
        $this->validarHorasDiarias($userId, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
        $this->validarSolapamientoUsuario($userId, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
        $this->validarHorarioSemanalJSON($datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
        $this->validarFestivo($datos['fecha']);

        // 2. BUSCAMOS LOS ESPACIOS FÍSICOS DE ESE TIPO
        $espacios = Espacio::where('tipo_espacio_id', $tipoEspacioId)
            ->where('disponible', 1)
            ->get();

        if ($espacios->isEmpty()) {
            throw new Exception("Actualmente no hay espacios operativos de este tipo.");
        }

        // 3. EL SABUESO: Probamos uno por uno contra TUS reglas estrictas
        foreach ($espacios as $espacio) {
            try {
                // Usamos tus propios métodos para comprobar buffer de limpieza y solapamientos
                $this->validarBufferLimpieza($espacio->id, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
                $this->validarDisponibilidadReal($espacio, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);

                // Si pasa tus dos escáneres sin tirar Exception... ¡ES EL ELEGIDO!
                return $espacio;
            } catch (Exception $e) {
                // Si este espacio choca o necesita limpieza, ignoramos el error y probamos el siguiente
                continue;
            }
        }

        // 4. Si termina el bucle y no ha devuelto nada...
        throw new Exception("No quedan espacios disponibles en el horario y fecha seleccionados.");
    }

    // =========================================================================
    // VALIDACIONES PRIVADAS
    // =========================================================================

    /**
     * Comprueba el horario semanal guardado en configuración (JSON).
     * Verifica que el día esté abierto, que la reserva entre dentro del horario
     * y que termine antes del margen de desalojo previo al cierre.
     */
    private function validarHorarioSemanalJSON($fecha, $horaInicio, $horaFin)
    {
        $horarioJson = Configuracion::get('horario_semanal');

        if (!$horarioJson) return;

        // Comprobar si el valor ya es un array (por el casting del modelo)
        $horarioSemanal = is_string($horarioJson) ? json_decode($horarioJson, true) : $horarioJson;

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
        $horarioHoy = $horarioSemanal[$nombreDia] ?? null;

        // Validar que el día existe en la config y está marcado como abierto
        $estaAbierto = $horarioHoy && filter_var($horarioHoy['abierto'], FILTER_VALIDATE_BOOLEAN);

        if (!$estaAbierto) {
            throw new Exception("La biblioteca permanece cerrada los " . ucfirst($nombreDia) . "s.");
        }

        // Convierte una hora a minutos desde medianoche.
        // Si es hora de cierre y llega "00:00", lo tratamos como fin de día (1440 min).
        $toMinutos = function ($hora, $esCierre = false) {
            $parsed = Carbon::parse($hora);
            $minutos = $parsed->hour * 60 + $parsed->minute;

            // Solo si es una hora de cierre/fin y marca "00:00", asumimos que es el final del día (1440 min)
            if ($esCierre && $minutos === 0) {
                return 1440;
            }
            return $minutos;
        };

        // Pasamos 'true' solo a las variables que representan un final/cierre
        $inicioReserva = $toMinutos($horaInicio);
        $finReserva    = $toMinutos($horaFin, true);
        $apertura      = $toMinutos($horarioHoy['apertura']);
        $cierre        = $toMinutos($horarioHoy['cierre'], true);

        $aperturaStr = Carbon::parse($horarioHoy['apertura'])->format('H:i');
        $cierreStr   = Carbon::parse($horarioHoy['cierre'])->format('H:i');

        // 1. Validar que la reserva esté dentro del horario de apertura general
        if ($inicioReserva < $apertura || $finReserva > $cierre) {
            throw new Exception("El horario de los " . ucfirst($nombreDia) . " es de {$aperturaStr} a {$cierreStr}.");
        }

        // 2. Aplicar margen de desalojo antes del cierre (ej: 15 minutos)
        $margenDesalojo = 15;
        $cierrePermitido = $cierre - $margenDesalojo;

        if ($finReserva > $cierrePermitido) {
            // Formatear los minutos de vuelta a H:i para el mensaje de error
            $horas = str_pad(floor($cierrePermitido / 60), 2, '0', STR_PAD_LEFT);
            $minutos = str_pad($cierrePermitido % 60, 2, '0', STR_PAD_LEFT);
            $horaMaxima = "{$horas}:{$minutos}";

            throw new Exception("Las reservas deben terminar como máximo a las {$horaMaxima} para permitir el desalojo de la sala.");
        }
    }

    /**
     * Evita que alguien intente reservar una hora que ya pasó.
     * Usa la zona horaria de Madrid para no tener problemas con el servidor.
     */
    private function validarHoraNoPasada($fecha, $horaInicio)
    {
        $fechaHoraReserva = Carbon::createFromFormat(
            'Y-m-d H:i',
            $fecha . ' ' . $horaInicio,
            'Europe/Madrid'
        );

        if ($fechaHoraReserva->lessThan(now())) {
            throw new Exception("La hora que intentas reservar ya pasó.");
        }
    }

    /**
     * Comprueba que la reserva dure entre el mínimo y máximo de minutos
     * configurados en el panel (por defecto entre 30 y 180 minutos).
     */
    private function validarDuracion($horaInicio, $horaFin)
    {
        $duracionMinima = Configuracion::get('duracion_minima', 30);
        $duracionMaxima = Configuracion::get('duracion_maxima', 180);

        $diferencia = Carbon::parse($horaInicio)->diffInMinutes(Carbon::parse($horaFin));

        if ($diferencia < $duracionMinima) {
            throw new Exception("La duración mínima de una reserva es {$duracionMinima} minutos.");
        }
        if ($diferencia > $duracionMaxima) {
            throw new Exception("La duración máxima de una reserva es {$duracionMaxima} minutos.");
        }
    }

    /**
     * Si el usuario tiene una sanción vigente hoy o en el futuro, le bloqueamos la reserva.
     */
    private function validarSanciones($userId)
    {
        $usuario = User::find($userId);
        $sancionActiva = $usuario->sanciones()->where('fecha_fin', '>=', Carbon::today())->first();

        if ($sancionActiva) {
            throw new Exception("Tienes una sanción activa hasta {$sancionActiva->fecha_fin->format('d/m/Y')}. No puedes reservar.");
        }
    }

    /**
     * Comprueba que el usuario no supere el número máximo de reservas activas
     * permitidas simultáneamente (solo cuenta las que todavía no han pasado).
     */
    private function validarLimiteReservas($userId)
    {
        $maximaReserva = Configuracion::get('max_reservas_activas', 2);

        $reservasActivas = Reserva::where('user_id', $userId)
            ->where('estado', 'activa')
            ->where(function ($query) {              // ← Solo las que aún no han pasado
                $query->where('fecha_reserva', '>', now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->where('fecha_reserva', now()->toDateString())
                            ->where('hora_fin', '>', now()->format('H:i:s'));
                    });
            })
            ->count();

        if ($reservasActivas >= $maximaReserva) {
            throw new Exception("Has superado el límite de {$maximaReserva} reservas activas.");
        }
    }

    /**
     * Suma todos los minutos reservados por el usuario en ese día
     * y comprueba que la nueva reserva no supere el máximo diario configurado.
     */
    private function validarHorasDiarias($userId, $fecha, $horaInicio, $horaFin)
    {
        $minutosMaximos = Configuracion::get('max_horas_diarias', 360);

        $reservasHoy = Reserva::where('user_id', $userId)
            ->where('fecha_reserva', $fecha)
            ->where('estado', 'activa')
            ->get();

        $minutosConsumidos = 0;
        foreach ($reservasHoy as $reserva) {
            $minutosConsumidos += Carbon::parse($reserva->hora_inicio)->diffInMinutes(Carbon::parse($reserva->hora_fin));
        }

        $nuevosMinutos = Carbon::parse($horaInicio)->diffInMinutes(Carbon::parse($horaFin));

        if (($minutosConsumidos + $nuevosMinutos) > $minutosMaximos) {
            $horas = $minutosMaximos / 60;
            throw new Exception("Excedes el límite diario de {$horas} horas permitidas.");
        }
    }

    /**
     * Evita que el mismo usuario tenga dos reservas activas que se pisen en el tiempo,
     * independientemente del espacio. No puedes estar en dos sitios a la vez.
     */
    private function validarSolapamientoUsuario($userId, $fecha, $horaInicio, $horaFin)
    {
        $solapamiento = Reserva::where('user_id', $userId)
            ->whereDate('fecha_reserva', $fecha)
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio)
            ->where('estado', 'activa') // Añadido para que las canceladas no cuenten como solapamiento
            ->exists();

        if ($solapamiento) {
            throw new Exception('Ya tienes otra reserva activa en este horario. ¡No puedes estar en dos sitios a la vez!');
        }
    }

    /**
     * Verifica que el espacio exista, esté disponible y tenga capacidad mayor que cero.
     */
    private function validarEspacioActivoYCapacidad($espacioId)
    {
        $espacio = Espacio::find($espacioId);
        if (!$espacio || !$espacio->disponible) {
            throw new Exception('El espacio seleccionado no existe o está en mantenimiento.');
        }
        if ($espacio->capacidad <= 0) {
            throw new Exception('Este espacio tiene un error de configuración y no admite reservas.');
        }
    }

    /**
     * Añade un margen de limpieza (configurable, por defecto 15 min) antes y después
     * de cada reserva existente, y comprueba que la nueva no choque con ese margen.
     */
    private function validarBufferLimpieza($espacioId, $fecha, $horaInicio, $horaFin)
    {
        $antelacion = Configuracion::get('buffer_limpieza', 15);
        $inicioExpandido = Carbon::createFromTimeString($horaInicio)->subMinutes($antelacion)->format('H:i:s');
        $finExpandido = Carbon::createFromTimeString($horaFin)->addMinutes($antelacion)->format('H:i:s');

        $hayChoque = Reserva::where('espacio_id', $espacioId)
            ->where('fecha_reserva', $fecha)
            ->where('estado', 'activa') // Añadido para ignorar canceladas
            ->where('hora_inicio', '<', $finExpandido)
            ->where('hora_fin', '>', $inicioExpandido)
            ->exists();

        if ($hayChoque) {
            throw new Exception('La sala requiere tiempo de limpieza o choca con otra reserva.');
        }
    }

    /**
     * Comprueba que el número de reservas activas en ese espacio y franja horaria
     * no haya alcanzado ya la capacidad máxima del espacio.
     */
    private function validarDisponibilidadReal($espacio, $fecha, $horaInicio, $horaFin)
    {
        $numeroSolapamiento = Reserva::where('espacio_id', $espacio->id)
            ->where('fecha_reserva', $fecha)
            ->where('estado', 'activa')
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio)
            ->count();

        if ($numeroSolapamiento >= $espacio->capacidad) {
            throw new Exception('La capacidad de este espacio está completa en este horario.');
        }
    }

    /**
     * Solo los admins pueden reservar en nombre de otro usuario.
     * Si el user_id del formulario no coincide con el logueado, se comprueba el rol.
     */
    private function validarPermisoUsuario($userIdFormulario, $usuarioLogueadoId)
    {
        if ($userIdFormulario && $userIdFormulario != $usuarioLogueadoId) {
            $user = User::find($usuarioLogueadoId);
            if ($user->rol !== 'admin') {
                throw new Exception('Acción denegada: No puedes reservar en nombre de otra persona.');
            }
        }
    }

    /**
     * Consulta la tabla de festivos y lanza excepción si la fecha elegida está marcada como festivo.
     */
    private function validarFestivo($fecha)
    {
        $motivoFestivo = Festivo::where('fecha', $fecha)->value('motivo');
        if ($motivoFestivo) {
            throw new Exception("El centro estará cerrado ese día por ser festivo: {$motivoFestivo}.");
        }
    }

    /**
     * Impide reservar con menos antelación de la mínima configurada (por defecto 30 minutos).
     */
    private function validarAntelacionMinima($fecha, $horaInicio)
    {
        $minutosMinimos = (int) Configuracion::get('antelacion_minima', 30);

        $inicioReserva = Carbon::parse($fecha . ' ' . $horaInicio, 'Europe/Madrid');
        $limiteMinimo = now('Europe/Madrid')->addMinutes($minutosMinimos);

        if ($inicioReserva->isBefore($limiteMinimo)) {
            throw new Exception("Debes reservar con al menos {$minutosMinimos} minutos de antelación.");
        }
    }

    /**
     * Impide reservar con demasiada antelación para evitar que alguien acapare espacios
     * semanas o meses por adelantado. El límite en días se configura desde el panel.
     */
    private function validarAntelacionMaxima($fecha)
    {
        $diasMaximos = (int) Configuracion::get('dias_maximos_reserva', 7);

        $inicioReserva = Carbon::parse($fecha);
        $limiteMaximo = now()->addDays($diasMaximos)->endOfDay();

        if ($inicioReserva->isAfter($limiteMaximo)) {
            throw new Exception("Solo puedes reservar con un máximo de {$diasMaximos} días de antelación.");
        }
    }
}
