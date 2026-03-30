<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function index()
    {
        return view('autentifikasi', [
            'users'  => User::all(),
            'roles'  => Role::all(),
            'pendingUsers' => User::where('account_status', 'pending')->get(),
            'active' => 'autentifikasi'
        ]);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/home');
        }
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role'         => 'required|string|exists:roles,name',
            'nomor_induk'  => 'nullable|string|max:255|unique:users,nomor_induk,' . $id,
        ], [
            'nomor_induk.unique' => 'NIP/NISN sudah digunakan oleh pengguna lain.',
        ]);

        $user = User::findOrFail($id);

        // Update nomor_induk jika diisi
        if ($request->filled('nomor_induk')) {
            $user->nomor_induk = $request->nomor_induk;
            $user->save();
        }

        // Update role
        $role = Role::where('name', $request->input('role'))->first();
        if ($role) {
            $user->syncRoles([$role->name]);
        }

        $user->account_status = 'active';
        $user->save();

        return redirect()->route('autentifikasi.index')->with('success', 'Data pengguna berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();
        return redirect()->route('autentifikasi.index')->with('success', 'Role pengguna berhasil dihapus');
    }
}
