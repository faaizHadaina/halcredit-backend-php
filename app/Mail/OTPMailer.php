<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;


class OTPMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationLink;

    /**
     * Create a new message instance.
     *
     * @param User|mixed $user The user object or data associated with the user.
     * @param string $otp The one-time password to be sent to the user.
     */
    public function __construct($user, $verificationLink)
    {
        $this->user = $user;
        $this->verificationLink = $verificationLink;
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
        return $this->subject('OTP Confirmation')
                ->view('mails.otp');
    }
}
