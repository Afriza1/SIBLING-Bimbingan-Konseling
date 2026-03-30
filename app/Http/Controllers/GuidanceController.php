<?php

namespace App\Http\Controllers;

use App\Models\Guidance;
use App\Models\GuidanceBooking;
use App\Models\Student;
use App\Models\User;
use App\Models\Classes;
use App\Models\Major;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GuidanceController extends Controller
{
    use WaliKelasFilter;
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

    public function index(Request $request)
    {
        $role    = $this->getRole();
        $user    = auth()->user();
        $isSiswa = $role === 'Siswa';

        $selectedMonth = $request->input('month', now()->format('m'));
        $selectedYear  = $request->input('year', now()->format('Y'));
        $selectedClass = $request->input('class');

        $classes = Classes::all();
        $majors  = Major::all();
        $users   = User::all();
        $years   = range(now()->year - 5, now()->year);

        if ($isSiswa) {
            $student = HomeController::autoLinkStudent($user);

            $guidances = $student
                ? Guidance::with(['student', 'user'])
                    ->where('student_id', $student->id)
                    ->whereYear('date', $selectedYear)
                    ->whereMonth('date', $selectedMonth)
                    ->get()
                : collect([]);

            $students        = $student ? collect([$student]) : collect([]);
            $confirmedBookings = collect([]);
        } else {
            $isWaliKelas = ($role === 'Wali Kelas');

            if ($isWaliKelas) {
                // Wali Kelas hanya lihat bimbingan siswa di kelasnya
                $myClass   = $this->getMyClass();
                $studentIds = $myClass
                    ? Student::where('class_id', $myClass->id)->pluck('id')
                    : collect([]);

                $guidances = Guidance::with(['student', 'user'])
                    ->whereIn('student_id', $studentIds)
                    ->whereYear('date', $selectedYear)
                    ->whereMonth('date', $selectedMonth)
                    ->get();

                $students = $myClass
                    ? Student::with('class.major')->where('class_id', $myClass->id)->get()
                    : collect([]);
            } else {
                $guidances = Guidance::with(['student', 'user'])
                    ->whereYear('date', $selectedYear)
                    ->whereMonth('date', $selectedMonth)
                    ->get();

                $students = Student::with('class.major')->when($selectedClass, fn($q) => $q->where('class_id', $selectedClass))->get();
            }

            // Ambil booking yang sudah confirmed (hanya untuk Guru BK & Admin)
            $confirmedBookings = $isWaliKelas ? collect([]) : GuidanceBooking::where('status', 'confirmed')
                ->orderBy('booking_date')
                ->get();
        }

        return view('data_bimbingan', compact(
            'guidances', 'students', 'classes', 'majors', 'users',
            'selectedMonth', 'selectedYear', 'selectedClass', 'years',
            'isSiswa', 'confirmedBookings'
        ), ['active' => 'guidance']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'topics'            => 'required|string|max:255',
            'notes'             => 'required|string|max:255',
            'date'              => 'required|date',
            'proof_of_guidance' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'student_id'        => 'required|integer',
            'user_id'           => 'required|integer',
            'booking_id'        => 'nullable|integer|exists:guidance_bookings,id',
        ]);

        $lastGuidance  = Guidance::where('student_id', $request->student_id)
            ->orderBy('guidance_count', 'desc')->first();
        $guidanceCount = $lastGuidance ? $lastGuidance->guidance_count + 1 : 1;

        $guidance                 = new Guidance();
        $guidance->topics         = $request->topics;
        $guidance->notes          = $request->notes;
        $guidance->date           = $request->date;
        $guidance->student_id     = $request->student_id;
        $guidance->user_id        = $request->user_id;
        $guidance->guidance_count = $guidanceCount;

        if ($request->hasFile('proof_of_guidance')) {
            $guidance->proof_of_guidance = $request->file('proof_of_guidance')
                ->store('guidances', 'public');
        }

        $guidance->save();

        // Jika dari booking, ubah status booking jadi 'completed'
        if ($request->filled('booking_id')) {
            GuidanceBooking::where('id', $request->booking_id)
                ->update(['status' => 'completed']);
        }

        return redirect()->route('guidance.index')->with('success', 'Bimbingan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'topics'            => 'required|string|max:255',
            'notes'             => 'required|string|max:255',
            'date'              => 'required|string|max:255',
            'student_id'        => 'required|string|max:255',
            'user_id'           => 'required|string|max:255',
            'proof_of_guidance' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $guidance             = Guidance::findOrFail($id);
        $guidance->topics     = $request->topics;
        $guidance->notes      = $request->notes;
        $guidance->date       = $request->date;
        $guidance->student_id = $request->student_id;
        $guidance->user_id    = $request->user_id;

        if (is_null($guidance->guidance_count) || $guidance->guidance_count == 0) {
            $last = Guidance::where('student_id', $request->student_id)
                ->orderBy('guidance_count', 'desc')->first();
            $guidance->guidance_count = $last ? $last->guidance_count + 1 : 1;
        }

        if ($request->hasFile('proof_of_guidance')) {
            if ($guidance->proof_of_guidance && Storage::disk('public')->exists($guidance->proof_of_guidance)) {
                Storage::disk('public')->delete($guidance->proof_of_guidance);
            }
            $guidance->proof_of_guidance = $request->file('proof_of_guidance')
                ->store('guidances', 'public');
        }

        $guidance->save();
        return redirect()->route('guidance.index')->with('success', 'Data bimbingan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Guidance::findOrFail($id)->delete();
        return redirect()->route('guidance.index')->with('success', 'Bimbingan berhasil dihapus!');
    }
}
