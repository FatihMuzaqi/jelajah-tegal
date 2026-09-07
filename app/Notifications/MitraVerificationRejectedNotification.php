<?php

namespace App\Notifications;

use App\Models\Mitra;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitraVerificationRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Mitra $mitra,
        public readonly string $reason,
        public readonly ?string $ownerName = null,
        public readonly ?string $ownerEmail = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $contactUrl = url('/tentang-kami');
        $recipientName = $this->ownerName ?? ($notifiable->name ?? 'Calon Mitra Jelajah Tegal');
        $recipientEmail = $this->ownerEmail ?? ($notifiable->email ?? ($notifiable->routes['mail'] ?? null));

        return (new MailMessage)
            ->subject('Pemberitahuan Status Pendaftaran Mitra "' . $this->mitra->display_name . '" — Jelajah Tegal')
            ->view('emails.mitra-rejected', [
                'mitra' => $this->mitra,
                'reason' => $this->reason,
                'recipientName' => $recipientName,
                'recipientEmail' => $recipientEmail,
                'contactUrl' => $contactUrl,
            ]);
    }
}
