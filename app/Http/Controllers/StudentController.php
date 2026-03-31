<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Status;
use App\Models\Classes;
use Illuminate\Http\Request;
use App\Imports\StudentsImport;
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class StudentController extends Controller
{
    use WaliKelasFilter;

    protected $ClassModel;
    protected $StatusModel;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->ClassModel  = new Classes();
        $this->StatusModel = new Status();
    }

    public function index()
    {
        $role = $this->getUserRole();

        if ($role === 'Wali Kelas') {
            $myClass  = $this->getMyClass();
            $students = $myClass
                ? Student::with(['class.major', 'status'])->where('class_id', $myClass->id)->get()
                : collect([]);
        } else {
            $students = Student::with(['class.major', 'status'])->get();
        }

        return view('data_siswa', [
            'students'    => $students,
            'classes'     => Classes::all(),
            'statuses'    => Status::all(),
            'active'      => 'student',
            'isWaliKelas' => $role === 'Wali Kelas',
            'myClass'     => $role === 'Wali Kelas' ? ($myClass ?? null) : null,
        ]);
    }

    public function create()
    {
        return view('student.create', ['active' => 'student']);
    }

    public function showImage($id)
    {
        $student = Student::findOrFail($id);
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            $path     = Storage::disk('public')->path($student->photo);
            $mime     = mime_content_type($path);
            $contents = Storage::disk('public')->get($student->photo);
            return response($contents)->header('Content-Type', $mime);
        }
        abort(404, 'Foto tidak ditemukan.');
    }

    public function download($id)
    {
        $student = Student::findOrFail($id);
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            $path     = Storage::disk('public')->path($student->photo);
            $mime     = mime_content_type($path);
            $ext      = pathinfo($student->photo, PATHINFO_EXTENSION);
            $contents = Storage::disk('public')->get($student->photo);
            return response($contents)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', "attachment; filename=foto_siswa_{$student->id}.{$ext}");
        }
        return redirect()->back()->with('error', 'Foto tidak ditemukan.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn'                  => 'required|string|max:255',
            'name'                  => 'required|string|max:255',
            'gender'                => 'required|string|max:255',
            'place_of_birth'        => 'required|string|max:255',
            'date_of_birth'         => 'required|date',
            'religion'              => 'required|string|max:255',
            'phone_number'          => 'required|string|max:255',
            'address'               => 'required|string|max:255',
            'photo'                 => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'admission_date'        => 'required|date',
            'guardian_name'         => 'required|string|max:255',
            'guardian_phone_number' => 'required|string|max:255',
            'class_id'              => 'required|string|max:255',
            'status_id'             => 'required|string|max:255',
            'email'                 => 'required|email',
        ]);

        $student = new Student();
        $student->fill($request->only([
            'nisn','name','gender','place_of_birth','date_of_birth',
            'religion','phone_number','address','admission_date',
            'guardian_name','guardian_phone_number','class_id','status_id','email'
        ]));

        if ($request->hasFile('photo')) {
            $student->photo = $request->file('photo')->store('students', 'public');
        }

        $student->save();
        return redirect()->route('student.index')->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls']);
        Excel::import(new StudentsImport, $request->file('file'));
        return back()->with('success', '✅ Data siswa berhasil diimport!');
    }

    public function downloadFormat()
    {
        $spreadsheet = new Spreadsheet();
        $sheet1      = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Format Siswa');
        $headers = ['NISN','Nama','Kelas','Jenis Kelamin','Tempat Lahir','Tanggal Lahir','Agama','No Telepon','Alamat','Nama Wali','Nomor Telepon Wali','Email','Status','Tanggal Masuk'];
        $sheet1->fromArray([$headers], null, 'A1');

        $classSheet = new Worksheet($spreadsheet, 'Daftar Kelas');
        $spreadsheet->addSheet($classSheet);
        $classSheet->fromArray(['ID','Kelas','Jurusan','Ruang'], null, 'A1');
        $classes   = Classes::with('major')->get();
        $classData = $classes->map(fn($c) => [$c->id, $c->class_level, $c->major->major_name, $c->classroom])->toArray();
        $classSheet->fromArray($classData, null, 'A2');

        $statusSheet = new Worksheet($spreadsheet, 'Daftar Status');
        $spreadsheet->addSheet($statusSheet);
        $statusSheet->fromArray(['ID','Status'], null, 'A1');
        $statusSheet->fromArray(Status::all()->map(fn($s) => [$s->id, $s->status_name])->toArray(), null, 'A2');

        for ($i = 2; $i <= 1000; $i++) {
            $dv = $sheet1->getCell('D'.$i)->getDataValidation();
            $dv->setType(DataValidation::TYPE_LIST);
            $dv->setFormula1('"Laki-laki,Perempuan"');
            $dv->setAllowBlank(false)->setShowDropDown(true);
        }
        $sheet1->getStyle('F2:F1000')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $sheet1->getStyle('N2:N1000')->getNumberFormat()->setFormatCode('yyyy-mm-dd');

        $writer = new Xlsx($spreadsheet);
        return response()->stream(fn() => $writer->save('php://output'), 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="format_import_siswa.xlsx"',
        ]);
    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nisn'                  => 'required|string|max:255',
            'name'                  => 'required|string|max:255',
            'gender'                => 'required|string|max:255',
            'place_of_birth'        => 'required|string|max:255',
            'date_of_birth'         => 'required|date',
            'religion'              => 'required|string|max:255',
            'phone_number'          => 'required|string|max:255',
            'address'               => 'required|string|max:255',
            'photo'                 => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'admission_date'        => 'required|date',
            'guardian_name'         => 'required|string|max:255',
            'guardian_phone_number' => 'required|string|max:255',
            'class_id'              => 'required|string|max:255',
            'status_id'             => 'required|string|max:255',
            'email'                 => 'required|email',
        ]);

        $student = Student::findOrFail($id);
        $student->fill($request->only([
            'nisn','name','gender','place_of_birth','date_of_birth',
            'religion','phone_number','address','admission_date',
            'guardian_name','guardian_phone_number','class_id','status_id','email'
        ]));

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $student->photo = $request->file('photo')->store('students', 'public');
        }

        $student->save();
        return redirect()->route('student.index')->with('success', 'Data siswa berhasil diubah');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }
        $student->delete();
        return redirect()->route('student.index')->with('success', 'Siswa berhasil dihapus!');
    }
}
