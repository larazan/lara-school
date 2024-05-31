<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Stream extends Model
{
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'name',
        // 'school_id'
    ];

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

        if (Auth::user()->hasRole('Super Admin')) {
            return $query;
        }

        return $query;
    }
}
