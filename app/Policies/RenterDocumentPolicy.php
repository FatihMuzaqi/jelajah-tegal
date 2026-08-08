<?php
namespace App\Policies;
use App\Models\RenterDocument; use App\Models\User;
class RenterDocumentPolicy { public function view(User $user,RenterDocument $document): bool { return $document->user_id===$user->id||$user->can('renter-documents.review'); } public function update(User $user,RenterDocument $document): bool { return $document->user_id===$user->id&&$document->status->value==='pending'; } }
