<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ClassTeacher extends Model
{
    use HasFactory;

    protected $fillable = array(
        "class_section_id",
        "teacher_id",
        // "school_id",
    );

    protected $appends = ['class_id'];

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Super Admin')) {
            return $query;
        }

        if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
            return $query;
        }

        if (Auth::user()->hasRole('Student')) {
            return $query;
        }

        return $query;
    }

    public function class_section() {
        return $this->belongsTo(ClassSection::class, 'class_section_id')->withTrashed();
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id')->withTrashed();
    }


    public function getClassIdAttribute(){
        if ($this->relationLoaded('class_section')) {
            return $this->class_section->class_id;
        }
    }
}
