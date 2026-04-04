<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(10);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(StoreUsuarioRequest $request)
    {

        $datos = $request->validated();

        $datos['rol'] = 'usuario';

        User::create($datos);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado con éxito');
    }

    public function show(User $usuario)
    {
        $usuario->load('prestamos', 'reservas', 'sanciones');
        return view('usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {

        return view('usuarios.edit', compact('usuario'));
    }

    public function update(UpdateUsuarioRequest $request, User $usuario)
    {

        $datos = $request->validated();

        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $usuario->fill($datos);

        if (!$usuario->isDirty()) {
            return redirect()->route('usuarios.index')
                ->with('info', 'No realizaste ningún cambio.');
        }

        $usuario->save();

        return redirect()->route('usuarios.index')
            ->with('success', 'El usuario ha sido actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {




        if ($usuario->reservas()->count() > 0 || $usuario->prestamos()->count() > 0 || $usuario->sanciones()->count() > 0) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes borrar un usuario que contenga reservas, préstamos o sanciones activas.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'El usuario ha sido eliminado con éxito.');
    }

    //MÉTODOS DE AUTENTICACIÓN

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('categorias.index');
        }
        return view('usuarios.login');
    }

    public function authenticate(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('categorias.index');
        }

        return back()->withErrors([
            'email' => 'El email o la contraseña son incorrectos.'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('usuarios.login');
    }
}
