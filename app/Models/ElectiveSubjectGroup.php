<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ElectiveSubjectGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_subjects',
        'total_selectable_subjects',
        'class_id',
        'semester_id',
        // 'school_id'
    ];

    public function subjects()
    {
//        return $this->hasMany(ClassSubject::class, 'elective_subject_group_id');
        return $this->belongsToMany(Subject::class, ClassSubject::class, 'elective_subject_group_id', 'subject_id')->wherePivot('type', 'Elective')->withPivot('id as class_subject_id')->withTrashed();
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
