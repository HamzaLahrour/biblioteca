<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StorePrestamoRequest;
use App\Services\PrestamoService;
use App\Models\Prestamo;
use App\Models\Libro;
use App\Models\User;
use App\Models\Configuracion;
use Exception;




class PrestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    protected $prestamoService;

    /**
     * Inyectamos nuestro super-servicio en el constructor.
     * Así lo tenemos disponible en todos los métodos con $this->prestamoService
     */
    public function __construct(PrestamoService $prestamoService)
    {
        $this->prestamoService = $prestamoService;
    }

    public function index()
    {
        $query = Prestamo::with(['user', 'libro']);

        // 1. Filtro de Estado (El que ya teníamos)
        $query->when(request('estado'), function ($q, $estado) {
            $q->where('estado', $estado);
        });

        // 2. Filtro de Fecha: Desde
        $query->when(request('desde'), function ($q, $desde) {
            $q->whereDate('fecha_prestamo', '>=', $desde);
        });

        // 3. Filtro de Fecha: Hasta
        $query->when(request('hasta'), function ($q, $hasta) {
            $q->whereDate('fecha_prestamo', '<=', $hasta);
        });

        // 4. Ordenamiento Dinámico (Por defecto: más recientes primero)
        $orden = request('orden', 'desc');
        $query->orderBy('fecha_prestamo', $orden);

        $prestamos = $query->paginate(15);

        return view('prestamos.index', compact('prestamos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        $libros = Libro::where('estado', '!=', 'en_reparacion')->orderBy('titulo')->get();

        $diasPrestamo = Configuracion::get('dias_prestamo', 15);
        $fechaPorDefecto = now()->addDays($diasPrestamo)->format('Y-m-d');

        return view('prestamos.create', compact('usuarios', 'libros', 'fechaPorDefecto', 'diasPrestamo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePrestamoRequest $request)
    {
        try {
            $this->prestamoService->crearPrestamo($request->validated(), auth()->id());

            return redirect()->route('prestamos.index')
                ->with('success', 'Préstamo registrado correctamente. El stock ha sido actualizado.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Prestamo $prestamo)
    {
        $prestamo->load(['user', 'libro', 'sancion']);
        return view('prestamos.show', compact('prestamo'));
    }


    public function devolver(Prestamo $prestamo)
    {
        try {
            $resultado = $this->prestamoService->devolverPrestamo($prestamo);

            $mensaje = 'Libro devuelto con éxito.';
            if ($resultado['tarde']) {
                $mensaje .= " ATENCIÓN: Devuelto con {$resultado['dias_retraso']} días de retraso. Se ha generado una sanción automática.";
                return redirect()->route('prestamos.index')->with('warning', $mensaje);
            }

            return redirect()->route('prestamos.index')->with('success', $mensaje);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function renovar(Prestamo $prestamo)
    {
        try {
            $this->prestamoService->renovarPrestamo($prestamo, auth()->id());
            return back()->with('success', 'El préstamo ha sido renovado y el tiempo extendido exitosamente.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function perdido(Prestamo $prestamo)
    {
        try {
            $this->prestamoService->marcarComoPerdido($prestamo, auth()->id());
            return back()->with('danger', 'El préstamo se ha marcado como PERDIDO. Se ha aplicado la sanción correspondiente al alumno.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
