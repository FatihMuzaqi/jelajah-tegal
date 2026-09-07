<?php

namespace App\Notifications;

use App\Models\Mitra;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitraVerificationApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Mitra $mitra,
        public readonly ?string $ownerName = null,
        public readonly ?string $ownerEmail = null,
        public readonly ?string $adminNotes = null,
        public readonly ?string $autoLoginUrl = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = route('login');
        $portalUrl = route('mitra.dashboard');
        $autoLoginUrl = $this->autoLoginUrl ?? $loginUrl;
        $recipientName = $this->ownerName ?? ($notifiable->name ?? 'Mitra Jelajah Tegal');
        $recipientEmail = $this->ownerEmail ?? ($notifiable->email ?? ($notifiable->routes['mail'] ?? null));

        return (new MailMessage)
            ->subject('Selamat! Pendaftaran Mitra "' . $this->mitra->display_name . '" Telah Disetujui — Jelajah Tegal')
            ->view('emails.mitra-approved', [
                'mitra' => $this->mitra,
                'recipientName' => $recipientName,
                'recipientEmail' => $recipientEmail,
                'loginUrl' => $loginUrl,
                'portalUrl' => $portalUrl,
                'autoLoginUrl' => $autoLoginUrl,
                'adminNotes' => $this->adminNotes,
            ]);
    }
}
