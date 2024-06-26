<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PromoteStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_section_id',
        'session_year_id',
        'result',
        'status',
        // 'school_id',
    ];

    public function student() {
        return $this->belongsTo(Students::class)->withTrashed();
    }

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Super Admin')) {
            return $query;
        }

        if (Auth::user()->hasRole('School Admin')) {
            return $query;
        }

        if (Auth::user()->hasRole('Student')) {
            return $query;
        }

        return $query;
    }
}
