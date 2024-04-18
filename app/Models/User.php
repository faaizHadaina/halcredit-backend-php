<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\ExposePermissions;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;
    use ExposePermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_verified',
        'is_eligible',
        'is_completed',
        'is_invoice',
        'is_statement',
        'is_details',
        'is_guarantor',
        'email_verification_token',
        'has_role',
        'has_transaction_pin',
    ];

    // Add the profile relationship
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }


    protected $guard_name = "api";

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_reset_token',
        'email_verification_token',
        'transaction_pin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'                 => 'datetime',
        'date_of_birth'                     => 'date',
        'started_date'                      => 'date',
        'password_reset_expires'            => 'datetime',
        'is_verified'                       => 'boolean',
        'is_eligible'                       => 'boolean',
        'is_completed'                      => 'boolean',
        'is_invoice'                        => 'boolean',
        'is_statement'                      => 'boolean',
        'is_details'                        => 'boolean',
        'is_guarantor'                      => 'boolean',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setPasswordAttribute($value){
        $this->attributes['password'] = Hash::make($value);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function hasWallet(){
        return $this->hasOne('App\Models\Wallet', 'user_id', 'id');
    }

    public function hasProfile(){
        return $this->hasOne('App\Models\Profile', 'user_id', 'id');
    }

    public function format(){
        return [
            'id'                    => $this->id,
            'email'                 => $this->email,
            'name'                  => $this->name,
            'profile'               => $this->profile ? $this->profile->format() : null,
            'wallet'                => $this->wallet ? $this->wallet->format() : null,
            'is_verified'           => $this->is_verified,
            'is_eligible'           => $this->is_eligible,
            'is_completed'          => $this->is_completed,
            'is_invoice'            => $this->is_invoice,
            'is_statement'          => $this->is_statement,
            'is_details'            => $this->is_details,
            'is_guarantor'          => $this->is_guarantor,
            'has_role'              => $this->has_role,
            'has_transaction_pin'   => $this->has_transaction_pin,
            'activated'             => $this->activated,
            'permissions'           => $this->can,
            'roles'                 => $this->roles
        ];
    }
}
