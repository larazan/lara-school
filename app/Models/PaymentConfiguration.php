<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PaymentConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method',
        'api_key',
        'secret_key',
        'webhook_secret_key',
        'status',
        'currency_code',
        'currency_symbol',
        // 'school_id',
    ];

    public function scopeOwner($query)
    {
        if(Auth::user()){
            if (Auth::user()->hasRole('Super Admin')) {
                return $query->where('school_id',null);
            }

            if (Auth::user()->hasRole('School Admin')) {
                return $query;
            }

            if (Auth::user()->hasRole('Student')) {
                return $query;
            }

            if (Auth::user()->hasRole('Guardian')) {
                if(request('child_id')){
                    $childId = request('child_id');
                    $studentAuth = Students::where('id',$childId)->first();
                    return $query;
                }
                return $query;
            }
        }

        return $query;
    }
}
