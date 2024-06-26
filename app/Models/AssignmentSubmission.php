<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'session_year_id',
        'feedback',
        'points',
        'status',
        // 'school_id',
    ];

    public function file() {
        return $this->morphMany(File::class, 'modal');
    }

    public function assignment() {
        return $this->belongsTo(Assignment::class);
    }

    public function student() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function scopeOwner($query) {

        if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
            return $query;
        }

        if (Auth::user()->hasRole('Teacher')) {
            $subject_teacher = Auth::user()->subjectTeachers;
            $class_section_id = $subject_teacher->pluck('class_section_id');
            $subject_id = $subject_teacher->pluck('subject_id');

            return $query->whereHas('assignment', function ($q) use ($class_section_id, $subject_id) {
                $q->whereIn('class_section_id', $class_section_id)->whereIn('subject_id', $subject_id);
            });
        }

        if (Auth::user()->hasRole('Student')) {
            return $query;
        }
        return $query;
    }

    public function session_year() {
        return $this->belongsTo(SessionYear::class)->withTrashed();
    }
}
