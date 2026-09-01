<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitraInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $mitraName,
        public readonly string $token,
        public readonly ?string $recipientName = null,
        public readonly ?string $recipientEmail = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $activationUrl = route('mitra.activation.show', $this->token);
        $email = $this->recipientEmail ?? ($notifiable->email ?? ($notifiable->routes['mail'] ?? null));

        return (new MailMessage)
            ->subject('Undangan Aktivasi Akun Pengelola Mitra — Jelajah Tegal')
            ->view('emails.mitra-invitation', [
                'mitraName' => $this->mitraName,
                'token' => $this->token,
                'activationUrl' => $activationUrl,
                'recipientName' => $this->recipientName,
                'recipientEmail' => $email,
            ]);
    }
}
