<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AnnouncementClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id', 
        'class_section_id', 
        // 'school_id', 
        'class_subject_id'
    ];

    public function scopeOwner($query) {
        if (Auth::user()->hasRole('Super Admin')) {
            return $query;
        }

        if (Auth::user()->hasRole('School Admin')) {
            return $query;
        }

        if (Auth::user()->hasRole('Teacher')) {
            return $query;
        }

        if (Auth::user()->hasRole('Student')) {
            return $query;
        }

        return $query;
    }


    public function class_section() {
        return $this->belongsTo(ClassSection::class)->withTrashed();
    }


    public function class_subject() {
        return $this->belongsTo(ClassSubject::class);
    }
}
