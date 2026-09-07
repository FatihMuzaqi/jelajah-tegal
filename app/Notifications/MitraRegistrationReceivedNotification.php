<?php

namespace App\Notifications;

use App\Models\Mitra;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitraRegistrationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Mitra $mitra,
        public readonly ?string $ownerName = null,
        public readonly ?string $ownerEmail = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $recipientName = $this->ownerName ?? ($notifiable->name ?? 'Calon Mitra Jelajah Tegal');
        $recipientEmail = $this->ownerEmail ?? ($notifiable->email ?? ($notifiable->routes['mail'] ?? null));

        return (new MailMessage)
            ->subject('Pendaftaran Kemitraan "' . $this->mitra->display_name . '" Berhasil Dikirim — Jelajah Tegal')
            ->view('emails.mitra-registration-received', [
                'mitra' => $this->mitra,
                'recipientName' => $recipientName,
                'recipientEmail' => $recipientEmail,
            ]);
    }
}
