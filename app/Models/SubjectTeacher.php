<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SubjectTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_section_id',
        'subject_id',
        'teacher_id',
        'class_subject_id',
        // 'school_id',
    ];
    protected $appends = ['subject_with_name'];

    public function class_section() {
        return $this->belongsTo(ClassSection::class)->with('class', 'section', 'medium')->withTrashed();
    }

    public function subject() {
        return $this->belongsTo(Subject::class)->withTrashed();
    }

    public function teacher() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function semester() {
        return $this->belongsTo(Semester::class)->withTrashed();
    }

    public function scopeOwner($query) {
        if (Auth::user()->hasRole('School Admin')) {
            return $query;
        }

        if (Auth::user()->hasRole('Teacher')) {
            $cache = app(CachingService::class);
            $currentSemester = $cache->getDefaultSemesterData();
            $class_subject_ids = ClassSubject::where(['school_id' => Auth::user()->school_id])->where(function($query) use($currentSemester){
                (!empty($currentSemester)) ? $query->where('semester_id', $currentSemester->id)->orWhereNull('semester_id') : $query->orWhereNull('semester_id');
            })->pluck('id');
            /*Shakir sir's code*/
//            $teacher_class_subjects = SubjectTeacher::where(['teacher_id' => Auth::user()->id, 'school_id' => Auth::user()->school_id])->whereIn('class_subject_id',$class_subject_ids)->pluck('class_subject_id');
//            return $query->whereIn('class_subject_id',$teacher_class_subjects)->where('school_id', Auth::user()->school_id);

            /*Sagar Sir's Code*/
            return $query->whereIn('class_subject_id', $class_subject_ids)->where(['teacher_id' => Auth::user()->id, 'school_id' => Auth::user()->school_id]);
        }

        if (Auth::user()->hasRole('Student')) {
            return $query;
        }

        return $query;
    }

    public function scopeSubjectTeacher($query, $class_section_id = null)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher_id = $user->teacher()->pluck('id');
            return $query->whereIn('teacher_id', $teacher_id);
        }
        
        return $query;
    }

    public function getSubjectWithNameAttribute() {
        $name = '';
        if ($this->relationLoaded('subject')) {
            if (!empty($this->subject->name)) {
                $name .= $this->subject->name;
            }

            if (!empty($this->subject->type)) {
                $name .= ' (' . $this->subject->type . ')';
            }
        }

        if ($this->relationLoaded('teacher')) {
            $name .= ' - ' . $this->teacher->full_name;
        }
        return $name;
    }

    // public function scopeCurrentSemesterData($query){
    //     $currentSemester = app(SemesterInterface::class)->default();
    //     if($currentSemester){
    //         $query->where(function ($query) use($currentSemester){
    //             $query->where('semester_id', $currentSemester->id)->orWhereNull('semester_id');
    //         });
    //     }
    // }
}
