<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'no_induk' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'jenis_kelamin' => ['required', 'string', 'max:255'],
            'universitas' => ['required', 'string', 'max:255'],
            'prodi' => ['required', 'string', 'max:255'],
            'jabatan_fungsional' => ['required', 'string', 'max:255'],
            'pendidikan_tertinggi' => ['required', 'string', 'max:255'],
            'status_kerja' => ['required', 'string', 'max:255'],
            'status_dosen' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_induk' => $request->no_induk,
            'jenis_kelamin' => $request->jenis_kelamin,
            'universitas' => $request->universitas,
            'prodi' => $request->prodi,
            'jabatan_fungsional' => $request->jabatan_fungsional,
            'pendidikan_tertinggi' => $request->pendidikan_tertinggi,
            'status_kerja' => $request->status_kerja,
            'status_dosen' => $request->status_dosen,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
