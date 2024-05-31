<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Section extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 
        // 'school_id'
    ];

    public function classes()
    {
        return $this->belongsToMany(ClassSchool::class, 'class_sections', 'section_id', 'class_id')->withTrashed();
    }

    public function scopeOwner($query)
    {

        if (Auth::user()->hasRole('School Admin')) {
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
