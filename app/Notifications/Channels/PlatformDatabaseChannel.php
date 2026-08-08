<?php
namespace App\Notifications\Channels;
use App\Models\DatabaseNotification;
class PlatformDatabaseChannel
{
    public function send(object $notifiable, object $notification): DatabaseNotification
    {
        return DatabaseNotification::create(['user_id'=>$notifiable->getKey(),'mitra_id'=>$notification->mitraId(),'type'=>get_class($notification),'data'=>$notification->toArray($notifiable)]);
    }
}
