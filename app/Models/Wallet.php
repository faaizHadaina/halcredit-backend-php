<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_name',
        'bank_name',
        'wallet_balance',
        'currency',
        'transaction_pin',
        'bank_code',
        'account_number',
        'credit_score',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function format(){
        return [
            'id'                    => $this->id,
            'user_id'               => $this->user_id,
            'user'                  => $this->user,
            'account_name'          => $this->account_name,
            'bank_name'             => $this->bank_name,
            'bank_code'             => $this->bank_code,
            'currency'              => $this->currency,
            'account_number'        => $this->account_number,
            'wallet_balance'        => $this->wallet_balance,
            'credit_score'          => $this->credit_score,
            'status'                => $this->status
        ];
    }
}
