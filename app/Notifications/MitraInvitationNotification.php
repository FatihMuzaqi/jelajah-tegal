<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitraInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $mitraName, private readonly string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Undangan Mitra Lokantara')
            ->greeting('Anda diundang ke '.$this->mitraName)
            ->line('Gunakan tautan berikut untuk menerima undangan. Tautan hanya dapat digunakan satu kali.')
            ->action('Aktifkan akses Mitra', route('mitra.activation.show', $this->token))
            ->line('Abaikan email ini jika Anda tidak mengenali undangan tersebut.');
    }
}
