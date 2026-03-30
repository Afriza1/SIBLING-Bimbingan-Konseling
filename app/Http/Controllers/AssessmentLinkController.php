<?php

namespace App\Http\Controllers;

use App\Models\AssessmentLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AssessmentLinkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    private function getRole()
    {
        $user = auth()->user();
        return DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->value('roles.name');
    }

    public function index()
    {
        $role  = $this->getRole();
        $links = AssessmentLink::with('user')->latest()->get();

        return view('data_asesmen_link', [
            'links'   => $links,
            'role'    => $role,
            'active'  => 'assessment_link',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
            'url'         => 'required|url',
        ]);

        $link = new AssessmentLink();
        $link->name        = $request->name;
        $link->description = $request->description;
        $link->url         = $request->url;
        $link->user_id     = auth()->id();

        if ($request->hasFile('icon')) {
            $link->icon = $request->file('icon')->store('assessment_icons', 'public');
        }

        $link->save();
        return redirect()->route('assessment_link.index')->with('success', 'Asesmen berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
            'url'         => 'required|url',
        ]);

        $link = AssessmentLink::findOrFail($id);
        $link->name        = $request->name;
        $link->description = $request->description;
        $link->url         = $request->url;

        if ($request->hasFile('icon')) {
            if ($link->icon && Storage::disk('public')->exists($link->icon)) {
                Storage::disk('public')->delete($link->icon);
            }
            $link->icon = $request->file('icon')->store('assessment_icons', 'public');
        }

        $link->save();
        return redirect()->route('assessment_link.index')->with('success', 'Asesmen berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $link = AssessmentLink::findOrFail($id);
        if ($link->icon && Storage::disk('public')->exists($link->icon)) {
            Storage::disk('public')->delete($link->icon);
        }
        $link->delete();
        return redirect()->route('assessment_link.index')->with('success', 'Asesmen berhasil dihapus!');
    }
}
