<?php

namespace App\Helpers;

use Seshac\Otp\Otp;
use App\Models\Verification;

class Token{
    protected $otp;
    protected $identifier;
    public function __construct($identifier){
        $this->identifier = $identifier;

    }

    public function generateToken(){
        $this->otp = Otp::setValidity(300)
            ->setLength(4)
            ->setMaximumOtpsAllowed(10)
            ->setOnlyDigits(true)
            ->setUseSameToken(false)
            ->generate($this->identifier);
    }

    public function getToken(){
        $this->generateToken();
        return response()->json(['token' => $this->otp, 'expires' => Otp::expiredAt($this->identifier)]);
    }

    public function addOTPVerification($user_id, $token){
        return Verification::create([
            'user_id' => $user_id,
            'otp' => $token
        ]);
    }


    public function verifyOtp($otp){
        return $verify = Otp::validate($this->identifier, $otp);
    }

    public function markAsExpired(){
        return $expired = Otp::markAsExpired($this->identifier);
    }
}
