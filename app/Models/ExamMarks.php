<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExamMarks extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_timetable_id',
        'student_id',
        'class_subject_id',
        'obtained_marks',
        'passing_status',
        'session_year_id',
        'grade',
        // 'school_id',
    ];


    public function timetable()
    {
        return $this->belongsTo(ExamTimetable::class, 'exam_timetable_id');
    }

    public function class_subject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function subject()
    {
        /*Has Many through inverse*/
        return $this->hasManyThrough(Subject::class,ClassSubject::class,'id','id','class_subject_id','subject_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'student_id')->withTrashed();
    }

    public function scopeOwner($query)
    {

        if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
            return $query;
        }

        if (Auth::user()->hasRole('Student')) {
            return $query;
        }

        if (Auth::user()->hasRole('Super Admin')) {
            return $query;
        }

        return $query;
    }
}
