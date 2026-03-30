<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'company_name',
        'description',
        'location',
        'salary',
        'dateline_date',
        'pamphlet',
        'link',
        'user_id',
    ];

    protected $casts = [
        'dateline_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
