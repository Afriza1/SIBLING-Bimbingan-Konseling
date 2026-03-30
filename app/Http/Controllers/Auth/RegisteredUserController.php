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
            'nomor_induk' => ['required', 'string', 'max:255', 'unique:users'],
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nomor_induk.unique' => 'NISN/NIP sudah terdaftar.',
            'email.unique'       => 'Email sudah terdaftar.',
        ]);

        $user = User::create([
            'nomor_induk' => $request->nomor_induk,
            'name'        => $request->name,
            'email'       => $request->email ?? $request->nomor_induk . '@sibling.sch.id',
            'password'    => Hash::make($request->password),
            'account_status' => 'pending', //menunggu approval admin
        ]);

        // Assign role sementara (tanpa role dulu)
        // Admin yang akan assign role via halaman Autentifikasi User

        event(new Registered($user));

        Auth::login($user);

        //arahkan ke halaman tunggu approval
        return redirect()->route('pending');
    }
}
