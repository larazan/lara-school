<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'starting_range',
        'ending_range',
        'grade',
        // 'school_id',
        'created_at',
        'updated_at'
    ];

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
