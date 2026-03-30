<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentApiController extends Controller
{
    // Cari student_id berdasarkan nama — dipakai form bimbingan
    public function findByName(Request $request)
    {
        $name    = $request->input('name');
        $student = Student::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($name))])->first();

        if ($student) {
            return response()->json(['id' => $student->id, 'name' => $student->name]);
        }

        return response()->json(['id' => null, 'name' => null], 404);
    }
}
