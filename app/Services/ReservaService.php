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
        $this->validarDiaCierre($datos['fecha']);
        $this->validarHorarioBiblioteca($datos['hora_inicio'], $datos['hora_fin']);
        $this->validarHoraNoPasada($datos['fecha'], $datos['hora_inicio']);
        $this->validarAntelacionMinima($datos['fecha'], $datos['hora_inicio']);
        $this->validarAntelacionMaxima($datos['fecha']);
        $this->validarDuracion($datos['hora_inicio'], $datos['hora_fin']);
        $this->validarSanciones($userId);
        $this->validarLimiteReservas($userId);
        $this->validarHorasDiarias($userId, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
        $this->validarSolapamientoUsuario($userId, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin']);
        $this->validarEspacioActivoYCapacidad($datos['espacio_id']);

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

    private function validarHorarioBiblioteca($horaInicial, $horaFinal)
    {
        $horaInicioBib = Configuracion::get('hora_apertura', '08:00');
        $horaFinBib = Configuracion::get('hora_cierre', '21:00');

        if ($horaInicial < $horaInicioBib || $horaFinal > $horaFinBib) {
            throw new Exception("El horario de la biblioteca es de {$horaInicioBib} a {$horaFinBib}.");
        }
    }

    private function validarHoraNoPasada($fecha, $horaInicio)
    {
        if (Carbon::parse($fecha)->isToday() && Carbon::parse($horaInicio)->isPast()) {
            throw new Exception("La hora que intentas reservar ya pasó.");
        }
    }

    private function validarAntelacionMinima($fecha, $horaInicio)
    {
        $antelacion = Configuracion::get('antelacion_minima', 30);
        $inicioReserva = Carbon::parse($fecha . ' ' . $horaInicio);

        if (Carbon::now()->diffInMinutes($inicioReserva, false) < $antelacion) {
            throw new Exception("Debes reservar con al menos {$antelacion} minutos de antelación.");
        }
    }

    private function validarAntelacionMaxima($fecha)
    {
        $diasAntelacion = Configuracion::get('antelacion_maxima', 15);
        $fechaMax = Carbon::today()->addDays($diasAntelacion);

        if (Carbon::parse($fecha)->greaterThan($fechaMax)) {
            throw new Exception("No puedes reservar con más de {$diasAntelacion} días de antelación.");
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

    private function validarDiaCierre($fecha)
    {
        if (Carbon::parse($fecha)->isWeekend()) {
            throw new Exception('El centro permanece cerrado los fines de semana.');
        }

        // Aquí sí se queda 'fecha' porque la tabla Festivo usa esa columna
        $motivoFestivo = Festivo::where('fecha', $fecha)->value('motivo');
        if ($motivoFestivo) {
            throw new Exception("El centro estará cerrado ese día por ser festivo: {$motivoFestivo}.");
        }
    }
}
