<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build(): self
    {
        return $this->subject('Selamat Datang di ' . config('app.name') . '!')
            ->markdown('emails.welcome', [
                'user' => $this->user,
            ]);
    }
}