<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;

class AchievementController extends Controller
{
    use WaliKelasFilter;
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = auth()->user();

        // Ambil role dari database
        $role = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->value('roles.name');

        if ($role === 'Siswa') {
            // Siswa hanya lihat prestasi miliknya sendiri
            $student = Student::where('user_id', $user->id)->first();

            // Fallback: cari via nama jika user_id belum diset
            if (!$student) {
                $student = Student::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($user->name))])->first();
                // AUTO-LINK: simpan user_id agar next login langsung ketemu
                if ($student && is_null($student->user_id)) {
                    $student->user_id = $user->id;
                    $student->save();
                }
            }

            $achievements = $student
                ? Achievement::with(['student'])->where('student_id', $student->id)->get()
                : collect([]);

            return view('data_prestasi', [
                'achievements' => $achievements,
                'students'     => collect([]), // siswa tidak perlu dropdown list siswa
                'active'       => 'achievement',
                'is_siswa'     => true,
            ]);
        }

        // Wali Kelas → hanya lihat prestasi siswa di kelasnya
        if ($role === 'Wali Kelas') {
            $myClass    = $this->getMyClass();
            $studentIds = $myClass
                ? Student::where('class_id', $myClass->id)->pluck('id')
                : collect([]);

            return view('data_prestasi', [
                'achievements' => Achievement::with(['student'])->whereIn('student_id', $studentIds)->get(),
                'students'     => $myClass ? Student::where('class_id', $myClass->id)->get() : collect([]),
                'active'       => 'achievement',
                'is_siswa'     => false,
                'isWaliKelas'  => true,
                'myClass'      => $myClass,
            ]);
        }

        // Admin, Guru BK → lihat semua
        return view('data_prestasi', [
            'achievements' => Achievement::with(['student'])->get(),
            'students'     => Student::all(),
            'active'       => 'achievement',
            'is_siswa'     => false,
            'isWaliKelas'  => false,
            'myClass'      => null,
        ]);
    }

    public function showImage($id)
    {
        $achievement = Achievement::findOrFail($id);
        if ($achievement->certificate) {
            return response($achievement->certificate)
                ->header('Content-Type', 'image/jpeg');
        }
        abort(404, 'Gambar tidak ditemukan.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ranking'           => 'required|string|max:255',
            'achievements_name' => 'required|string|max:255',
            'level'             => 'required|string|max:255',
            'description'       => 'required',
            'type'              => 'required|string|max:255',
            'date'              => 'required|date',
            'recognition'       => 'required|string|max:255',
            'certificate'       => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'student_id'        => 'required|string|max:255',
        ]);

        $achievement = new Achievement();
        $achievement->ranking           = $request->ranking;
        $achievement->achievements_name = $request->achievements_name;
        $achievement->level             = $request->level;
        $achievement->description       = $request->description;
        $achievement->type              = $request->type;
        $achievement->date              = $request->date;
        $achievement->recognition       = $request->recognition;
        $achievement->student_id        = $request->student_id;

        if ($request->hasFile('certificate')) {
            $path = $request->file('certificate')->store('certificates', 'public');
            $achievement->certificate = $path;
        }
        $achievement->save();

        return redirect()->route('achievement.index')->with('success', 'Prestasi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ranking'           => 'required|string|max:255',
            'achievements_name' => 'required|string|max:255',
            'level'             => 'required|string|max:255',
            'description'       => 'required',
            'type'              => 'required|string|max:255',
            'date'              => 'required|date',
            'recognition'       => 'required|string|max:255',
            'certificate'       => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'student_id'        => 'required|string|max:255',
        ]);

        $achievement = Achievement::findOrFail($id);
        $achievement->ranking           = $request->input('ranking');
        $achievement->achievements_name = $request->input('achievements_name');
        $achievement->level             = $request->input('level');
        $achievement->description       = $request->input('description');
        $achievement->type              = $request->input('type');
        $achievement->date              = $request->input('date');
        $achievement->recognition       = $request->input('recognition');
        $achievement->student_id        = $request->input('student_id');

        if ($request->hasFile('certificate')) {
            $path = $request->file('certificate')->store('certificates', 'public');
            $achievement->certificate = $path;
        }
        $achievement->save();

        return redirect()->route('achievement.index', $achievement->id)->with('success', 'Pencapaian berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $achievement = Achievement::findOrFail($id);
        $achievement->delete();
        return redirect()->route('achievement.index')->with('success', 'Prestasi berhasil dihapus!');
    }
}
