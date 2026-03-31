<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAssessment;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\AssessmentLink;
use Illuminate\Support\Facades\DB;

class StudentAssessmentController extends Controller
{
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
        return view('data_asesmen_siswa', [
            'student_assessments' => StudentAssessment::with(['student', 'assessment'])->get(),
            'students'            => Student::with(['class', 'status'])->get(),
            'assessments'         => Assessment::all(),
            'links'               => AssessmentLink::with('user')->latest()->get(),
            'role'                => $this->getRole(),
            'active'              => 'student_assessment',
        ]);
    }

    public function create()
    {
        return view('student_assessment.create', ['active' => 'student_assessment']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|max:255',
            'answers'    => 'required|array',
            'answers.*'  => 'in:0,1',
        ]);

        foreach ($validated['answers'] as $assessment_id => $answer) {
            $sa = new StudentAssessment();
            $sa->student_id    = $validated['student_id'];
            $sa->assessment_id = $assessment_id;
            $sa->answer        = $answer;
            $sa->save();
        }

        return redirect()->route('student_assessment.index')->with('success', 'Data berhasil disimpan!');
    }

    public function export()
    {
        $student_assessments = StudentAssessment::all();

        $excelData = [['ID', 'Nama Siswa', 'Pertanyaan', 'Jawaban']];
        foreach ($student_assessments as $sa) {
            $excelData[] = [
                $sa->id,
                $sa->student->name,
                $sa->assessment->question,
                $sa->answer,
            ];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($excelData as $rowIndex => $row) {
            foreach ($row as $colIndex => $cellValue) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $cellValue);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->stream(fn() => $writer->save('php://output'), 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="export_asesmen_siswa.xlsx"',
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'answers'    => 'required|array',
        ]);

        foreach ($validated['answers'] as $assessmentId => $answer) {
            StudentAssessment::updateOrCreate(
                ['student_id' => $validated['student_id'], 'assessment_id' => $assessmentId],
                ['answer' => $answer]
            );
        }

        return redirect()->route('student_assessment.index')->with('success', 'Jawaban berhasil diperbarui');
    }

    public function destroy($id)
    {
        StudentAssessment::where('student_id', $id)->delete();
        return redirect()->route('student_assessment.index')->with('success', 'Data berhasil dihapus!');
    }
}
