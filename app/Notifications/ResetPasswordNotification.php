<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    protected string $frontendUrl;

    public function __construct(string $token)
    {
        parent::__construct($token);
        $this->frontendUrl = config('app.frontend_url');
    }

    public function toMail($notifiable)
    {
        $resetUrl = "{$this->frontendUrl}/reset-password"
            . "?token={$this->token}&email={$notifiable->email}";

      // Use a Blade view for HTML email
        return (new MailMessage)
            ->subject('Reset Your Password')
            ->view('emails.auth.reset-password', [
                'user' => $notifiable,
                'url' => $resetUrl,
            ]);
    }

   
}
