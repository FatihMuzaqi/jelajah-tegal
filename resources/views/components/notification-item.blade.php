@props(['notification'])
<a href='#notifications' class='notification-item {{ $notification->read_at ? '' : 'unread' }}'><span
        class='notification-icon'>N</span><span><strong>{{ data_get($notification->data, 'title', str($notification->type)->afterLast('\\')->headline()) }}</strong><small>{{ data_get($notification->data, 'message', 'Pembaruan Lokantara tersedia.') }}</small><time>{{ $notification->created_at?->diffForHumans() }}</time></span></a>
