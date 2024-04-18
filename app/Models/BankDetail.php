<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
class BankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'accountName',
        'accountNumber',
        'currency',
        'bankName',
        'BVN',
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function format()
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'accountName'   => $this->accountName,
            'accountNumber' => $this->accountNumber,
            'currency'      => $this->currency,
            'bankName'      => $this->bankName,
            'bankCode'      => $this->bankCode,
            'statement' => ($this->statement != null) ? URL::asset('/storage/' . $this->statement) : null,
            'BVN'           => $this->BVN
        ];
    }
}
