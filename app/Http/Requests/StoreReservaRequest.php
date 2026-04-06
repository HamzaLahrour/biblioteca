<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Configuracion;
use App\Models\User;
use App\Models\Reserva;

use Illuminate\Support\Carbon;
use Monolog\Handler\IFTTTHandler;

class StoreReservaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }






    private function validarHorarioBiblioteca($validator, $horaInicial, $horaFinal)
    {

        $horaInicioBib = Configuracion::get('hora_apertura');
        $horaFinBib = Configuracion::get('hora_cierre');

        if ($horaInicial < $horaInicioBib) {
            $validator->errors()->add(
                'hora_inicio',
                "La hora de inicio no puede ser antes de las {$horaInicioBib}."
            );
        }

        if ($horaFinal > $horaFinBib) {
            $validator->errors()->add(
                'hora_fin',
                "La hora de fin no puede ser después de las {$horaFinBib}."
            );
        }
    }

    private function validarHoraNoPasada($validator, $fecha, $horaInicio)
    {
        // 2. Si es hoy, que horaInicio > ahora

        if (Carbon::parse($fecha)->isToday()) {

            if (Carbon::parse($horaInicio)->isPast()) {
                $validator->errors()->add(
                    'hora_inicio',
                    "La hora que intentas reservar ya pasó."
                );
            }
        }
    }

    private function validarAntelacionMinima($validator, $fecha, $horaInicio)
    {
        // 3. Faltan al menos 30 min desde ahora
        $antelacion = Configuracion::get('antelacion_minima');

        $inicioReserva = Carbon::parse($fecha . ' ' . $horaInicio);
        $minutosRestantes = Carbon::now()->diffInMinutes($inicioReserva, false);

        if ($minutosRestantes < $antelacion) {
            $validator->errors()->add(
                'hora_inicio',
                "Debes reservar con al menos {$antelacion} minutos de antelación."
            );
        }
    }

    private function validarDuracion($validator, $horaInicio, $horaFin)
    {
        // 5. Diferencia entre 30 y 180 mins

        $duracionMinima = Configuracion::get('duracion_minima');
        $duracionMaxima = Configuracion::get('duracion_maxima');

        $horaInicio = Carbon::parse($horaInicio);
        $horaFin = Carbon::parse($horaFin);

        $diferencia = $horaInicio->diffInMinutes($horaFin);

        if ($diferencia < $duracionMinima) {
            $validator->errors()->add(
                'hora_final',
                "La duracion minima de una reserva es {$duracionMinima} minutos."
            );
        }

        if ($diferencia > $duracionMaxima) {
            $validator->errors()->add(
                'hora_fin',
                "La duracion maxima de una reserva es {$duracionMaxima} minutos."
            );
        }
    }



    private function validarSanciones($validator, $userId)
    {
        // 8. Sanciones activas

        $usuario = User::find($userId);
        $hoy = Carbon::today();

        $sancionActiva = $usuario->sanciones
            ->where('fecha_fin', '>=', $hoy)
            ->first();

        if ($sancionActiva) {
            $validator->errors()->add(
                'hora_inicio',
                "Tienes una sanción activa hasta {$sancionActiva->fecha_fin->format('d/m/Y')}. No puedes realizar reservas."
            );
        }
    }

    private function validarLimiteReservas($validator, $userId)
    {
        // 13. Máximo 2 activas
        $usuario = User::find($userId);

        $maximaReserva = Configuracion::get('max_reservas_activas');




        $reservasActivas = Reserva::where('user_id', $userId)
            ->where('estado', 'activa')
            ->count();

        if ($reservasActivas >= $maximaReserva) {
            $validator->errors()->add(
                'hora_inicio',
                "Has superado el exceso de reservas activas, recuerda que el limite es de {$maximaReserva} reservas activas."
            );
        }
    }

    private function validarHorasDiarias($validator, $userId, $fecha, $horaInicio, $horaFin)
    {
        // 14. Max 6h totales al día
    }

    private function validarSolapamientoUsuario($validator, $userId, $fecha, $horaInicio, $horaFin)
    {
        // 12. Que el user no esté en otra sala a esa hora
    }

    private function validarEspacioActivoYCapacidad($validator, $espacioId)
    {
        // 6 y 7. Espacio activo y capacidad > 0
    }

    private function validarBufferLimpieza($validator, $espacioId, $fecha, $horaInicio, $horaFin)
    {
        // 15. 15 minutos de margen
    }

    private function validarDisponibilidadReal($validator, $espacioId, $fecha, $horaInicio, $horaFin)
    {
        // 11 ⭐. El lockForUpdate y cuenta de reservas concurrentes
    }

    private function validarAntelacionMaxima($validator, $fecha)
    {
        // 4. No más de 15 días en el futuro
    }

    private function validarHorasDistintas($validator, $horaInicio, $horaFin)
    {
        // 16. hora_inicio != hora_fin
    }

    private function validarPermisoUsuario($validator, $userId)
    {
        // 10. Usuario normal no puede mandar user_id ajeno
    }

    private function validarDiaCierre($validator, $fecha)
    {
        // Domingo cerrado + festivos
    }
}
