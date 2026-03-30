<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

trait WaliKelasFilter
{
    private function getUserRole()
    {
        $user = auth()->user();
        return DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->value('roles.name');
    }

    // Ambil class_id yang diampu wali kelas
    private function getMyClassId()
    {
        $user    = auth()->user();
        $myClass = Classes::where('wali_kelas_id', $user->id)->first();
        return $myClass ? $myClass->id : null;
    }

    // Ambil objek kelas lengkap dengan relasi
    private function getMyClass()
    {
        $user = auth()->user();
        return Classes::with('major')->where('wali_kelas_id', $user->id)->first();
    }

    // Ambil student_ids berdasarkan role
    // - Wali Kelas: hanya siswa di kelasnya
    // - Lainnya: semua siswa (null = tidak filter)
    private function getStudentIdsForRole($role, $selectedClass = null)
    {
        if ($role === 'Wali Kelas') {
            $classId = $this->getMyClassId();
            if (!$classId) return collect([]);
            return Student::where('class_id', $classId)->pluck('id');
        }

        // Admin / Guru BK — filter by selectedClass jika ada
        if ($selectedClass) {
            return Student::where('class_id', $selectedClass)->pluck('id');
        }

        return null; // null = tidak filter, ambil semua
    }
}
