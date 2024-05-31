<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends Model
{
    use HasFactory;

    protected static function booted() {
        static::addGlobalScope('school', static function (Builder $builder) {
            if (Auth::check()) {
                if (Auth::user()->hasRole('Super Admin')) {
                    // Show only permissions only which are assigned Super Admin
                    $builder->whereHas('roles', function ($q) {
                        $q->where('name', 'Super Admin');
                    });
                }
                if (Auth::user()->hasRole('School Admin')) {
                    // Show only permissions which are not assigned to Super Admin
                    $builder->whereHas('roles', function ($q) {
                        $q->where('name', '!=', 'Super Admin')->where(function ($q) {
                            $q->where('name', 'School Admin');
                        });
                    });
                }
            }
        });
    }
}
