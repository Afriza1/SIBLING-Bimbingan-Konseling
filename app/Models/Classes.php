<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function getByName($className)
    {
        // Format yang diharapkan: "XI TKR 1"
        if (!preg_match('/^(\S+)\s+(.+)\s+(\d+)$/', $className, $matches)) {
            throw new \InvalidArgumentException("❌ Format nama kelas tidak valid: '{$className}'");
        }

        $classLevel = strtoupper($matches[1]);
        $majorName = ucwords(strtolower(trim($matches[2])));
        $classroom = (int) $matches[3];

        return $this->select('classes.*', 'majors.major_name')
            ->join('majors', 'classes.major_id', '=', 'majors.id')
            ->where('classes.class_level', $classLevel)
            ->whereRaw('LOWER(TRIM(majors.major_name)) = ?', [strtolower($majorName)])
            ->where('classes.classroom', $classroom)
            ->first();
    }

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }
}
