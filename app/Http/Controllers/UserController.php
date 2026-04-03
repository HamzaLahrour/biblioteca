<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuarioRequest;
use Illuminate\Support\Facades\Auth; 


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

    
    
    public function login()
    {
        if(Auth::check()){
            return redirect()->route('categorias.index');
        }
        return view('usuarios.login');
    }

    public function authenticate(StoreUsuarioRequest $request)
    {
        if(Auth::attempt($request->only('email', 'password'))){
            $request->session()->regenerate();
            return redirect()->route('categorias.index');
        }

        return back()->withErrors([
            'email' => 'El email o la contraseña son incorrectos.'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('usuarios.login');
    }
}
