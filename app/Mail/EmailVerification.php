<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class EmailVerification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $token) {}

    public function build(): self
    {
        return $this->subject('Verifikasi Email - ' . config('app.name'))
            ->markdown('emails.email-verification', [
                'user' => $this->user,
                'token' => $this->token,
                'url' => URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addDay(),
                    [
                        'id' => $this->user->getKey(),
                        'hash' => sha1($this->user->getEmailForVerification()),
                    ],
                ),
            ]);
    }
}
