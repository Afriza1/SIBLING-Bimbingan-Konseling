<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobVacancy;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JobVacancyController extends Controller
{
    protected $UserModel;
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['landing', 'showImage', 'download']);
        $this->UserModel = new User();
    }
    public function index()
    {
        return view('data_loker', [
            'job_vacancies' => JobVacancy::with([ 'user'])->get(),
            'users' => User::all(),
            'active' => 'job_vacancy'
        ]);
    }

    public function showLatestImage()
    {
        $latestJobVacancy = JobVacancy::latest()->first();

        if ($latestJobVacancy && $latestJobVacancy->pamphlet) {
            return response()->json([
                'pamphlet' => route('jobVacancy.showImage', $latestJobVacancy->id),
            ]);
        }

        return response()->json([
            'message' => 'Tidak ada pamflet terbaru.',
        ], 404);
    }

    public function landing()
    {
        $job_vacancies = JobVacancy::latest()->paginate(3);
        $latestJobVacancy = JobVacancy::latest()->first();
        return view('landing', compact('job_vacancies', 'latestJobVacancy'));
    }

    public function showImage($id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);

        if (!$jobVacancy->pamphlet) {
            abort(404, 'Pamflet tidak ditemukan');
        }

        // Cek beberapa kemungkinan ekstensi
        $basePath = storage_path('app/public/job_vacancies/');
        $possibleExtensions = ['png', 'jpg', 'jpeg', 'pdf'];

        foreach ($possibleExtensions as $ext) {
            $filePath = $basePath . $jobVacancy->pamphlet . '.' . $ext;
            if (file_exists($filePath)) {
                return response()->file($filePath);
            }
        }

        // Kalau tidak ada ekstensi, coba langsung
        $filePath = $basePath . $jobVacancy->pamphlet;
        if (file_exists($filePath)) {
            return response()->file($filePath);
        }

        abort(404, 'File tidak ditemukan');
    }

    public function download($id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);

        $filePath = storage_path('app/public/job_vacancies/' . $jobVacancy->pamphlet);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath);
    }

    public function store(Request $request)
    {
        $request->validate([
            'position' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'description' => 'required',
            'location' => 'required|string|max:255',
            'salary' => 'required|string|max:255',
            'dateline_date' => 'required|date',
            'pamphlet' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'link' => 'nullable|string|max:255',
            'user_id' => 'required|string|max:255',
        ]);

        $jobVacancy = new JobVacancy();
        $jobVacancy->position = $request->input('position');
        $jobVacancy->company_name = $request->input('company_name');
        $jobVacancy->description = $request->input('description');
        $jobVacancy->location = $request->input('location');
        $jobVacancy->salary = $request->input('salary');
        $jobVacancy->dateline_date = $request->input('dateline_date');

            if ($request->hasFile('pamphlet')) {
            $file = $request->file('pamphlet');

            // PERBAIKAN: Simpan dengan ekstensi asli
            $filename = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('job_vacancies', $filename, 'public');
            $jobVacancy->pamphlet = $filename; // Sekarang tersimpan: "1234567890_brosur.png"
            }

        $jobVacancy->link = $request->input('link');
        $jobVacancy->user_id = $request->input('user_id');
        $jobVacancy->save();

        return redirect()->route('jobVacancy.index')->with('success', 'Lowongan berhasil ditambahkan!');
    }

public function downloadFormat()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Format Loker');

    // Header kolom
    $sheet->fromArray([[
        'Posisi',
        'Nama Perusahaan',
        'Deskripsi',
        'Lokasi',
        'Gaji',
        'Tanggal Deadline',
        'Pamflet (URL / Path)',
        'Link Pendaftaran',
        'Nama User'
    ]], null, 'A1');

    // Format tanggal
    $sheet->getStyle('F2:F1000')->getNumberFormat()
        ->setFormatCode('yyyy-mm-dd');

    // Auto-size kolom
    foreach (range('A', 'I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Nama file dan response
    $filename = 'format_import_loker.xlsx';
    $writer = new Xlsx($spreadsheet);

    return response()->stream(function () use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'description' => 'required',
            'location' => 'required|string|max:255',
            'salary' => 'required|string|max:255',
            'dateline_date' => 'required|date',
            'pamphlet' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'link' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $jobVacancy = JobVacancy::findOrFail($id);

        if ($request->hasFile('pamphlet')) {
            // HAPUS FILE LAMA
            if ($jobVacancy->pamphlet) {
                $oldFilePath = storage_path('app/public/job_vacancies/' . $jobVacancy->pamphlet);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);  // Hapus file lama
                }
            }

            // Upload file baru dengan timestamp (untuk cache busting)
            $file = $request->file('pamphlet');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->storeAs('job_vacancies', $filename, 'public');

            $validated['pamphlet'] = $filename;
        }

        // Update data
        $jobVacancy->update($validated);

        return redirect()->route('jobVacancy.index')
                        ->with('success', 'Lowongan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);

        // Hapus file saat delete record
        if ($jobVacancy->pamphlet) {
            $filePath = storage_path('app/public/job_vacancies/' . $jobVacancy->pamphlet);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $jobVacancy->delete();

        return redirect()->route('jobVacancy.index')
                        ->with('success', 'Lowongan berhasil dihapus!');
    }
}
