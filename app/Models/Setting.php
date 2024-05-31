<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';
    protected $fillable = [
        'name',
        'data',
        'type',
    ];

    public $timestamps = false;

    public function getDataAttribute($value) {
        if ($this->attributes['type'] == 'file') {
            return url(Storage::url($value));
        }

        return $value;
    }
}
