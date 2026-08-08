@props(['status'])
@php($tone=match($status){'active','approved','enabled','ready'=>'success','pending','submitted','under_review','requested'=>'warning','rejected','suspended','failed','revoked'=>'danger',default=>'muted'})
<span class='status-badge status-{{ $tone }}'><span></span>{{ str($status)->replace('_',' ')->headline() }}</span>
