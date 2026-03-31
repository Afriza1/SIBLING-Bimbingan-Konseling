<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Lateness;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LatenessController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $userRoles = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->pluck('roles.name')
            ->map(fn($r) => strtolower(trim($r)));

        $isSiswa     = $userRoles->contains('siswa');
        $isWaliKelas = $userRoles->contains('wali kelas');
        $isGuruBK    = $userRoles->contains('guru bk');

        $selectedMonth = $request->input('month', now()->format('m'));
        $selectedYear  = $request->input('year', now()->format('Y'));
        $selectedClass = $request->input('class');
        $classes       = Classes::all();
        $years         = range(now()->year - 5, now()->year);

        $query = Lateness::with(['student.class.major', 'user'])
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear);

        if ($isSiswa) {
            $student = Student::where('user_id', $user->id)->first();
            if ($student) $query->where('student_id', $student->id);
        } elseif ($isWaliKelas) {
            $myClass = Classes::where('wali_kelas_id', $user->id)->first();
            if ($myClass) {
                $studentIds = Student::where('class_id', $myClass->id)->pluck('id');
                $query->whereIn('student_id', $studentIds);
            }
        } else {
            if ($selectedClass) {
                $studentIds = Student::where('class_id', $selectedClass)->pluck('id');
                $query->whereIn('student_id', $studentIds);
            }
        }

        $latenesses = $query->orderBy('date', 'desc')->orderBy('time_in', 'desc')->get();

        return view('data_keterlambatan', compact(
            'latenesses', 'classes', 'years',
            'selectedMonth', 'selectedYear', 'selectedClass',
            'isSiswa', 'isWaliKelas', 'isGuruBK'
        ), ['active' => 'lateness']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date'       => 'required|date',
            'time_in'    => 'required',
            'reason'     => 'nullable|string|max:255',
            'evidence'   => 'nullable|image|max:2048',
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence') && $request->file('evidence')->isValid()) {
            $evidencePath = $request->file('evidence')->store('latenesses', 'public');
        }

        Lateness::create([
            'student_id' => $request->student_id,
            'user_id'    => Auth::id(),
            'date'       => $request->date,
            'time_in'    => $request->time_in,
            'reason'     => $request->reason,
            'evidence'   => $evidencePath,
        ]);

        return redirect()->back()->with('success', 'Data keterlambatan berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $lateness = Lateness::findOrFail($id);

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date'       => 'required|date',
            'time_in'    => 'required',
            'reason'     => 'nullable|string|max:255',
            'evidence'   => 'nullable|image|max:2048',
        ]);

        $evidencePath = $lateness->evidence;
        if ($request->hasFile('evidence') && $request->file('evidence')->isValid()) {
            if ($evidencePath && Storage::disk('public')->exists($evidencePath)) {
                Storage::disk('public')->delete($evidencePath);
            }
            $evidencePath = $request->file('evidence')->store('latenesses', 'public');
        }

        $lateness->update([
            'student_id' => $request->student_id,
            'date'       => $request->date,
            'time_in'    => $request->time_in,
            'reason'     => $request->reason,
            'evidence'   => $evidencePath,
        ]);

        return redirect()->back()->with('success', 'Data keterlambatan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $lateness = Lateness::findOrFail($id);
        if ($lateness->evidence && Storage::disk('public')->exists($lateness->evidence)) {
            Storage::disk('public')->delete($lateness->evidence);
        }
        $lateness->delete();
        return redirect()->back()->with('success', 'Data keterlambatan berhasil dihapus!');
    }
}