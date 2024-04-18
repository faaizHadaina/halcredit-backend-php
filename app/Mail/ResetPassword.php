<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $otp;


    /**
     * Create a new message instance.
     *
     * @param User|mixed $user The user object or data associated with the user.
     * @param string $otp The one-time password to be sent to the user.
     */
    public function __construct($user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * This method returns the mailable object itself with the subject set and the view rendered with the necessary data.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Change Password OTP")
                ->view('mails.resetPassword_otp');
    }
}
