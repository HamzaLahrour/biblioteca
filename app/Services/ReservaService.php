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
     * Procesa y guarda una reserva aplicando todas las reglas de negocio críticas.
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

    // --- FUNCIONES PRIVADAS (Usando tu método Configuracion::get) ---

    private function validarHorarioSemanalJSON($fecha, $horaInicio, $horaFin)
    {
        $horarioJson = Configuracion::get('horario_semanal');

        if (!$horarioJson) return;

        // 1. BLINDAJE: Si Laravel ya lo hizo array, no lo decodificamos otra vez
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

        // 2. BLINDAJE: filter_var entiende '1', 'true', o true como Válido.
        $estaAbierto = $horarioHoy && filter_var($horarioHoy['abierto'], FILTER_VALIDATE_BOOLEAN);

        if (!$estaAbierto) {
            throw new Exception("La biblioteca permanece cerrada los " . ucfirst($nombreDia) . "s.");
        }

        // 3. Validamos horas (Cambiamos el formato por si el json dice "09:00" y la hora dice "09:00:00")
        $horaInicioReserva = Carbon::parse($horaInicio)->format('H:i');
        $horaFinReserva = Carbon::parse($horaFin)->format('H:i');
        $apertura = Carbon::parse($horarioHoy['apertura'])->format('H:i');
        $cierre = Carbon::parse($horarioHoy['cierre'])->format('H:i');

        if ($horaInicioReserva < $apertura || $horaFinReserva > $cierre) {
            throw new Exception("El horario de los " . ucfirst($nombreDia) . "s es de {$apertura} a {$cierre}.");
        }
    }

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

    private function validarSanciones($userId)
    {
        $usuario = User::find($userId);
        $sancionActiva = $usuario->sanciones()->where('fecha_fin', '>=', Carbon::today())->first();

        if ($sancionActiva) {
            throw new Exception("Tienes una sanción activa hasta {$sancionActiva->fecha_fin->format('d/m/Y')}. No puedes reservar.");
        }
    }

    private function validarLimiteReservas($userId)
    {
        $maximaReserva = Configuracion::get('max_reservas_activas', 2);
        $reservasActivas = Reserva::where('user_id', $userId)->where('estado', 'activa')->count();

        if ($reservasActivas >= $maximaReserva) {
            throw new Exception("Has superado el límite de {$maximaReserva} reservas activas.");
        }
    }

    private function validarHorasDiarias($userId, $fecha, $horaInicio, $horaFin)
    {
        $minutosMaximos = Configuracion::get('max_horas_diarias', 360);

        // CORRECCIÓN: 'fecha_reserva'
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

    private function validarSolapamientoUsuario($userId, $fecha, $horaInicio, $horaFin)
    {
        // CORRECCIÓN: 'fecha_reserva'
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

    private function validarBufferLimpieza($espacioId, $fecha, $horaInicio, $horaFin)
    {
        $antelacion = Configuracion::get('buffer_limpieza', 15);
        $inicioExpandido = Carbon::createFromTimeString($horaInicio)->subMinutes($antelacion)->format('H:i:s');
        $finExpandido = Carbon::createFromTimeString($horaFin)->addMinutes($antelacion)->format('H:i:s');

        // CORRECCIÓN: 'fecha_reserva'
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

    private function validarDisponibilidadReal($espacio, $fecha, $horaInicio, $horaFin)
    {
        // CORRECCIÓN: 'fecha_reserva'
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
     * Separamos la validación de los festivos para que quede limpia
     */
    private function validarFestivo($fecha)
    {
        $motivoFestivo = Festivo::where('fecha', $fecha)->value('motivo');
        if ($motivoFestivo) {
            throw new Exception("El centro estará cerrado ese día por ser festivo: {$motivoFestivo}.");
        }
    }

    private function validarAntelacionMinima($fecha, $horaInicio)
    {
        // Usamos TU clave original (30 minutos por defecto)
        $minutosMinimos = (int) Configuracion::get('antelacion_minima', 30);

        // Forzamos la zona horaria para evitar sustos con el servidor
        $inicioReserva = Carbon::parse($fecha . ' ' . $horaInicio, 'Europe/Madrid');
        $limiteMinimo = now('Europe/Madrid')->addMinutes($minutosMinimos);

        if ($inicioReserva->isBefore($limiteMinimo)) {
            throw new Exception("Debes reservar con al menos {$minutosMinimos} minutos de antelación.");
        }
    }

    /**
     * Valida que el usuario no acapare salas para dentro de 3 meses (Margen máximo a futuro)
     */
    private function validarAntelacionMaxima($fecha)
    {
        // Leemos tu nueva configuración (por defecto 7 días si falla la BD)
        $diasMaximos = (int) Configuracion::get('dias_maximos_reserva', 7);

        $inicioReserva = Carbon::parse($fecha);
        $limiteMaximo = now()->addDays($diasMaximos)->endOfDay();

        if ($inicioReserva->isAfter($limiteMaximo)) {
            throw new Exception("Solo puedes reservar con un máximo de {$diasMaximos} días de antelación.");
        }
    }
}
