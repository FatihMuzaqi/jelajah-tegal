<?php
namespace App\Notifications;
use App\Notifications\Channels\PlatformDatabaseChannel; use Illuminate\Bus\Queueable; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Notifications\Notification;
class DomainActivityNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public function __construct(private string $title,private string $message,private ?string $url=null,private ?string $mitraIdValue=null) {}
    public function via(object $notifiable): array { return [PlatformDatabaseChannel::class]; }
    public function toArray(object $notifiable): array { return ['title'=>$this->title,'message'=>$this->message,'url'=>$this->url]; }
    public function mitraId(): ?string { return $this->mitraIdValue ?? session('active_mitra_id'); }
}
