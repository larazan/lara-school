<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Subject extends Model
{
    use HasFactory;
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'bg_color',
        'image',
        'medium_id',
        'type',
        // 'school_id'
    ];
    protected $appends = ['name_with_type'];

    public function medium() {
        return $this->belongsTo(Mediums::class)->withTrashed();
    }

    public function class_subjects() {
        return $this->hasMany(ClassSubject::class);
    }

    //    public function scopeSubjectTeacher($query) {
    //        $user = Auth::user();
    //        if ($user->hasRole('Teacher')) {
    //            $subjects_ids = $user->teacher->subjects()->pluck('subject_id');
    //            return $query->whereIn('id', $subjects_ids);
    //        }
    //        return $query;
    //    }


    public function scopeOwner($query) {

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


    //Getter Attributes
    public function getImageAttribute($value) {
        return url(Storage::url($value));
    }

    public function getNameWithTypeAttribute() {
        $name = '';
        if (!empty($this->name)) {
            $name .= $this->name;
        }

        if (!empty($this->type)) {
            $name .= ' - ' . $this->type;
        }

        return $name;
    }
}
