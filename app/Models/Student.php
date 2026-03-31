<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'name',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'phone_number',
        'address',
        'photo',
        'admission_date',
        'guardian_name',
        'guardian_phone_number',
        'class_id',
        'status_id',
        'email',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function student_assessments()
    {
        return $this->hasMany(StudentAssessment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getByName($name)
    {
        return $this->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($name))])->first();
    }

    public function latenesses()
    {
        return $this->hasMany(Lateness::class);
    }
}
