<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'profile_picture',
        'status',
        'business_name',
        'industry',
        'description',
        'office_number',
        'address',
        'residential_number',
        'street_name',
        'zip_code',
        'state',
        'city',
        'country',
        'date_of_birth',
        'gender',
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
            'first_name'            => $this->first_name,
            'last_name'             => $this->last_name,
            'phone'                 => $this->phone,
            'profile_picture'       => ($this->profile_picture != null) ? URL::asset('/storage/' . $this->profile_picture) : null,
            'status'                => $this->status,
            'business_name'         => $this->business_name,
            'industry'              => $this->industry,
            'description'           => $this->description,
            'office_number'         => $this->office_number,
            'address'               => $this->address,
            'residential_number'    => $this->residential_number,
            'street_name'           => $this->street_name,
            'zip_code'              => $this->zip_code,
            'state'                 => $this->state,
            'city'                  => $this->city,
            'country'               => $this->country,
            'date_of_birth'         => $this->date_of_birth,
            'gender'                => $this->gender,
        ];
    }
}
