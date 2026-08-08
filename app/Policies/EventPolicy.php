<?php
namespace App\Policies;
use App\Models\Event; use App\Models\User;
class EventPolicy { public function update(User $user,Event $event): bool { return $user->can('event.manage')&&$event->catalogEntity->mitra_id===session('active_mitra_id'); } public function issueTicket(User $user,Event $event): bool { return $user->can('tickets.issue')&&$event->catalogEntity->mitra_id===session('active_mitra_id'); } }
