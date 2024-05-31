<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use JsonException;

class FormField extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'is_required',
        'default_values',
        'other',
        // 'school_id',
        'rank'
    ];

    protected $table = 'form_fields';

    public function scopeOwner($query) {

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

    // public function school() {
    //     return $this->belongsTo(School::class, 'school_id')->withTrashed();
    // }

    /**
     * @param $value
     * @return array|mixed
     * @throws JsonException
     */
    public function getDefaultValuesAttribute($value) {
        if (!empty($value) && !is_array($value)) {
            return json_decode($value, false, 512, JSON_THROW_ON_ERROR);
        }
        return $value;
    }
}
