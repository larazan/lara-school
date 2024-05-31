<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'title',
        'description',
        // 'school_id'
    ];

    protected $appends = ['default_date_format'];

    public function scopeOwner($query) {
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

    protected function setDateAttribute($value) {
        $this->attributes['date'] = date('Y-m-d', strtotime($value));
    }

    public function getDefaultDateFormatAttribute() {
        return date('d-m-Y', strtotime($this->date));
    }
}
