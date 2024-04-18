<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $table = 'verification';

    protected $fillable = ['user_id', 'otp'];

    public function scopeGetTokenLatest($query){
        return $query->where('user_id', 1)->latest()->first();
    }
}
