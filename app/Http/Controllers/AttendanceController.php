<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    use WaliKelasFilter;
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $selectedMonth = $request->input('month', now()->format('m'));
        $selectedYear  = $request->input('year', now()->format('Y'));
        $view          = $request->input('view', 'daily');

        $classes = Classes::all();
        $majors  = Major::all();
        $users   = User::all();
        $years   = range(now()->year - 5, now()->year);

        $userRoles = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->pluck('roles.name')
            ->map(fn($r) => strtolower(trim($r)));

        $isSiswa     = $userRoles->contains('siswa');
        $isWaliKelas = $userRoles->contains('wali kelas');

        if ($isSiswa) {
            $selectedClass = null;
        } elseif ($isWaliKelas) {
            $myClass       = $this->getMyClass();
            $selectedClass = $myClass ? $myClass->id : null;
        } else {
            $selectedClass = $request->input('class', optional(Classes::first())->id);
        }

        $dates       = [];
        $attendances = collect();
        $students    = collect();

        if ($isSiswa) {
            $students = Student::where('user_id', $user->id)->get();
            if ($students->isEmpty()) {
                $students = Student::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($user->name))])->get();
                foreach ($students as $s) {
                    if (is_null($s->user_id)) { $s->user_id = $user->id; $s->save(); }
                }
            }
        } elseif ($selectedClass) {
            $students = Student::where('class_id', $selectedClass)->get();
        }

        $studentIds = $students->pluck('id');

        if ($view === 'daily') {
            $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
            $dates[] = ['date' => $selectedDate, 'isWeekend' => Carbon::parse($selectedDate)->isWeekend()];
            $attendances = Attendance::with(['student', 'user'])
                ->whereDate('date', $selectedDate)
                ->whereIn('student_id', $studentIds)
                ->get();

            return view('data_absensi', compact(
                'attendances', 'students', 'classes', 'majors', 'users',
                'dates', 'selectedMonth', 'selectedYear', 'selectedClass',
                'years', 'view', 'isSiswa', 'isWaliKelas'
            ), ['active' => 'attendance']);
        }

        if ($view === 'monthly') {
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
            $endDate   = $startDate->copy()->endOfMonth();
            for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
                $dates[] = ['date' => $d->format('Y-m-d'), 'isWeekend' => $d->isWeekend()];
            }
            $attendances = Attendance::with('student')
                ->whereYear('date', $selectedYear)
                ->whereMonth('date', $selectedMonth)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('student_id');

            return view('data_absensi', compact(
                'attendances', 'students', 'classes', 'majors', 'users',
                'dates', 'selectedMonth', 'selectedYear', 'selectedClass',
                'years', 'view', 'isSiswa', 'isWaliKelas'
            ), ['active' => 'attendance']);
        }

        if ($view === 'semester') {
            $attendances = Attendance::with('student')
                ->whereYear('date', $selectedYear)
                ->whereIn('student_id', $studentIds)
                ->get();

            $semester      = $request->input('semester', 'ganjil');
            $bulanSemester = $semester === 'ganjil' ? [7,8,9,10,11,12] : [1,2,3,4,5,6];

            $semesterBreakdown = [];
            foreach ($students as $student) {
                foreach ($bulanSemester as $m) {
                    $semesterBreakdown[$student->id][$m] = ['Sakit' => 0, 'Ijin' => 0, 'Alpa' => 0];
                }
            }
            foreach ($attendances as $att) {
                $month = Carbon::parse($att->date)->month;
                if (!in_array($month, $bulanSemester)) continue;
                $status = $att->presence_status;
                if (isset($semesterBreakdown[$att->student_id][$month][$status])) {
                    $semesterBreakdown[$att->student_id][$month][$status]++;
                }
            }

            return view('data_absensi', compact(
                'attendances', 'students', 'classes', 'majors', 'users',
                'dates', 'selectedMonth', 'selectedYear', 'selectedClass',
                'years', 'view', 'semesterBreakdown', 'semester', 'isSiswa', 'isWaliKelas'
            ), ['active' => 'attendance']);
        }

        return view('data_absensi', compact(
            'attendances', 'students', 'classes', 'majors', 'users',
            'dates', 'selectedMonth', 'selectedYear', 'selectedClass',
            'years', 'view', 'isSiswa', 'isWaliKelas'
        ), ['active' => 'attendance']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'presence_status' => 'required|in:Hadir,Ijin,Sakit,Alpa',
            'izin_via'        => 'nullable|string|max:50',
            'keterangan'      => 'nullable|string|max:255',
            'date'            => 'nullable|date',
            'evidence'        => 'nullable|image|max:2048',
        ]);

        $date = $request->input('date', now()->toDateString());

        $existing = Attendance::where('date', $date)
            ->where('student_id', $validated['student_id'])
            ->first();

        $evidencePath = $existing?->evidence ?? null;

        if ($request->hasFile('evidence') && $request->file('evidence')->isValid()) {
            if ($evidencePath && Storage::disk('public')->exists($evidencePath)) {
                Storage::disk('public')->delete($evidencePath);
            }
            $evidencePath = $request->file('evidence')->store('attendances', 'public');
        }

        $attendance = Attendance::updateOrCreate(
            ['date' => $date, 'student_id' => $validated['student_id']],
            [
                'presence_status' => $validated['presence_status'],
                'description'     => $validated['keterangan'] ?? $validated['izin_via'],
                'user_id'         => Auth::id(),
                'evidence'        => $evidencePath,
            ]
        );

        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function showEvidence($id)
    {
        $attendance = Attendance::findOrFail($id);
        if ($attendance->evidence && Storage::disk('public')->exists($attendance->evidence)) {
            $path     = Storage::disk('public')->path($attendance->evidence);
            $mime     = mime_content_type($path);
            $contents = Storage::disk('public')->get($attendance->evidence);
            return response($contents)->header('Content-Type', $mime);
        }
        abort(404, 'Bukti tidak ditemukan.');
    }

    public function addStudent(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'class_id' => 'required|exists:classes,id']);
        Student::create(['name' => $request->name, 'class_id' => $request->class_id]);
        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function update(Request $request, $student_id)
    {
        $request->validate([
            'presence_status' => 'required|array',
            'user_id'         => 'required|exists:users,id',
            'month'           => 'required|numeric',
            'year'            => 'required|numeric',
        ]);
        foreach ($request->input('presence_status') as $date => $status) {
            Attendance::updateOrCreate(
                ['student_id' => $student_id, 'date' => $date],
                ['presence_status' => $status, 'user_id' => auth()->id()]
            );
        }
        return redirect()->back()->with('success', 'Absensi berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        Attendance::where('student_id', $id)
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->delete();
        return redirect()->route('attendance.index')->with('success', 'Data absensi berhasil dihapus.');
    }
}
