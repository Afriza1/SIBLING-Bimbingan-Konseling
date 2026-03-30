<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        return view('data_user', [
            'users'  => User::with('roles')->get(),
            'roles'  => Role::all(),
            'active' => 'user'
        ]);
    }

    public function settings()
    {
        return view('settings', [
            'users'  => User::with('roles')->get(),
            'roles'  => Role::all(),
            'active' => 'settings'
        ]);
    }

    public function showImage($id)
    {
        $user = User::findOrFail($id);
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            $path     = Storage::disk('public')->path($user->photo);
            $mime     = mime_content_type($path);
            $contents = Storage::disk('public')->get($user->photo);
            return response($contents)->header('Content-Type', $mime);
        }
        abort(404, 'Foto tidak ditemukan.');
    }

    public function download($id)
    {
        $user = User::findOrFail($id);
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            $path     = Storage::disk('public')->path($user->photo);
            $mime     = mime_content_type($path);
            $ext      = pathinfo($user->photo, PATHINFO_EXTENSION);
            $contents = Storage::disk('public')->get($user->photo);
            return response($contents)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', "attachment; filename=foto_user_{$user->id}.{$ext}");
        }
        return redirect()->back()->with('error', 'Foto tidak ditemukan.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users', 'public');
        }

        User::create($data);
        return redirect()->route('user.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nomor_induk'    => 'nullable|string|max:255|unique:users,nomor_induk,' . $id,
            'name'           => 'required|string|max:255',
            'gender'         => 'nullable|string|max:255',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth'  => 'nullable|date',
            'religion'       => 'nullable|string|max:255',
            'phone_number'   => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'photo'          => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'email'          => 'required|email|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id);
        $user->nomor_induk    = $request->input('nomor_induk');
        $user->name           = $request->input('name');
        $user->gender         = $request->input('gender');
        $user->place_of_birth = $request->input('place_of_birth');
        $user->date_of_birth  = $request->input('date_of_birth');
        $user->religion       = $request->input('religion');
        $user->phone_number   = $request->input('phone_number');
        $user->address        = $request->input('address');
        $user->email          = $request->input('email');

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('users', 'public');
        }

        $user->save();
        return redirect()->route('user.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function setting_account(Request $request, $id)
    {
        $request->validate([
            'nomor_induk'    => 'nullable|string|max:255|unique:users,nomor_induk,' . $id,
            'name'           => 'required|string|max:255',
            'gender'         => 'nullable|string|max:255',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth'  => 'nullable|date',
            'religion'       => 'nullable|string|max:255',
            'phone_number'   => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'photo'          => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'email'          => 'required|email|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id);
        $user->nomor_induk    = $request->input('nomor_induk');
        $user->name           = $request->input('name');
        $user->gender         = $request->input('gender');
        $user->place_of_birth = $request->input('place_of_birth');
        $user->date_of_birth  = $request->input('date_of_birth');
        $user->religion       = $request->input('religion');
        $user->phone_number   = $request->input('phone_number');
        $user->address        = $request->input('address');
        $user->email          = $request->input('email');

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('users', 'public');
        }

        $user->save();
        return redirect()->route('user.settings')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->delete();
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
