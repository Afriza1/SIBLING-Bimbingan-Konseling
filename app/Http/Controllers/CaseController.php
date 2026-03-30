<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cases;
use App\Models\Student;
use App\Models\User;
use App\Models\Classes;
use App\Models\Major;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CaseController extends Controller
{
    use WaliKelasFilter;
    protected $UserModel;
    protected $StudentModel;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->UserModel = new User();
        $this->StudentModel = new Student();
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

        $dates = [];
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $endDate   = $startDate->copy()->endOfMonth();
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $dates[] = ['date' => $date->format('Y-m-d'), 'isWeekend' => $date->isWeekend()];
        }

        $classes = Classes::all();
        $majors  = Major::all();
        $users   = User::all();
        $years   = range(now()->year - 5, now()->year);

        if ($isSiswa) {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                $student = Student::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($user->name))])->first();
                if ($student && is_null($student->user_id)) {
                    $student->user_id = $user->id;
                    $student->save();
                }
            }

            $cases = $student
                ? Cases::with(['student', 'user'])
                    ->where('student_id', $student->id)
                    ->whereYear('date', $selectedYear)
                    ->whereMonth('date', $selectedMonth)
                    ->get()
                : collect([]);

            $students = $student ? collect([$student]) : collect([]);
        } else {
            $isWaliKelas = ($role === 'Wali Kelas');

            if ($isWaliKelas) {
                $myClass    = $this->getMyClass();
                $studentIds = $myClass
                    ? Student::where('class_id', $myClass->id)->pluck('id')
                    : collect([]);

                $cases = Cases::with(['student', 'user'])
                    ->whereIn('student_id', $studentIds)
                    ->whereYear('date', $selectedYear)
                    ->whereMonth('date', $selectedMonth)
                    ->get();

                $students = $myClass
                    ? Student::with('class.major')->where('class_id', $myClass->id)->orderByRaw('LOWER(name)')->get()
                    : collect([]);
            } else {
                $cases = Cases::with(['student', 'user'])
                    ->whereYear('date', $selectedYear)
                    ->whereMonth('date', $selectedMonth)
                    ->get();

                $students = Student::with('class.major')
                    ->when($selectedClass, fn($q) => $q->where('class_id', $selectedClass))
                    ->orderByRaw('LOWER(name)')
                    ->get();
            }
        }

        return view('data_kasus', compact(
            'cases', 'students', 'classes', 'majors', 'users', 'dates',
            'selectedMonth', 'selectedYear', 'selectedClass', 'years', 'isSiswa'
        ), ['active' => 'case']);
    }

    public function showImage($id)
    {
        $case = Cases::findOrFail($id);
        if ($case->evidence && Storage::disk('public')->exists($case->evidence)) {
            $path     = Storage::disk('public')->path($case->evidence);
            $mime     = mime_content_type($path);
            $contents = Storage::disk('public')->get($case->evidence);
            return response($contents)->header('Content-Type', $mime);
        }
        abort(404, 'Bukti tidak ditemukan.');
    }

    // public function download($id)
    // {
    //     $case = Cases::findOrFail($id);
    //     if ($case->evidence && Storage::disk('public')->exists($case->evidence)) {
    //         $path      = Storage::disk('public')->path($case->evidence);
    //         $mime      = mime_content_type($path);
    //         $ext       = pathinfo($case->evidence, PATHINFO_EXTENSION);
    //         $contents  = Storage::disk('public')->get($case->evidence);
    //         return response($contents)
    //             ->header('Content-Type', $mime)
    //             ->header('Content-Disposition', "attachment; filename=bukti_kasus_{$case->id}.{$ext}");
    //     }
    //     return redirect()->back()->with('error', 'Bukti tidak ditemukan.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'case_name'   => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'resolution'  => 'required|string|max:255',
            'case_point'  => 'required|string|max:255',
            'date'        => 'required|date',
            'evidence'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'student_id'  => 'required|string|max:255',
            'user_id'     => 'required|string|max:255',
        ]);

        $case              = new Cases();
        $case->case_name   = $request->case_name;
        $case->description = $request->description;
        $case->resolution  = $request->resolution;
        $case->case_point  = $request->case_point;
        $case->date        = Carbon::parse($request->date)->format('Y-m-d H:i:s');
        $case->student_id  = $request->student_id;
        $case->user_id     = $request->user_id;

        if ($request->hasFile('evidence')) {
            // Simpan ke storage/app/public/cases/
            $case->evidence = $request->file('evidence')->store('cases', 'public');
        }

        $case->save();

        return redirect()->route('case.index')->with('success', 'Kasus berhasil ditambahkan!');
    }

    public function export()
    {
        $cases = Cases::all();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['ID','Nama Kasus','Deskripsi','Solusi','Poin','Tanggal','Bukti','Guru BK','Nama Siswa']], null, 'A1');
        foreach ($cases as $i => $case) {
            $sheet->fromArray([[
                $case->id, $case->case_name, $case->description, $case->resolution,
                $case->case_point, $case->date, $case->evidence,
                optional($case->user)->name, optional($case->student)->name
            ]], null, 'A'.($i+2));
        }
        $writer = new Xlsx($spreadsheet);
        return response()->stream(fn() => $writer->save('php://output'), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="export_kasus.xlsx"',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'case_name'   => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'resolution'  => 'required|string|max:255',
            'case_point'  => 'required|string|max:255',
            'date'        => 'required|date',
            'evidence'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'student_id'  => 'required|string|max:255',
            'user_id'     => 'required|string|max:255',
        ]);

        $case              = Cases::findOrFail($id);
        $case->case_name   = $request->case_name;
        $case->description = $request->description;
        $case->resolution  = $request->resolution;
        $case->case_point  = $request->case_point;
        $case->date        = Carbon::parse($request->date)->format('Y-m-d H:i:s');
        $case->student_id  = $request->student_id;
        $case->user_id     = $request->user_id;

        if ($request->hasFile('evidence')) {
            // Hapus file lama jika ada
            if ($case->evidence && Storage::disk('public')->exists($case->evidence)) {
                Storage::disk('public')->delete($case->evidence);
            }
            $case->evidence = $request->file('evidence')->store('cases', 'public');
        }

        $case->save();

        return redirect()->route('case.index')->with('success', 'Kasus berhasil diupdate!');
    }

    public function destroy($id)
    {
        $case = Cases::findOrFail($id);
        // Hapus file bukti jika ada
        if ($case->evidence && Storage::disk('public')->exists($case->evidence)) {
            Storage::disk('public')->delete($case->evidence);
        }
        $case->delete();
        return redirect()->route('case.index')->with('success', 'Kasus berhasil dihapus!');
    }
}
