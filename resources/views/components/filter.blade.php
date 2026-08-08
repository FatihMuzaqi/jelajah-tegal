@props(['action'=>url()->current()])
<form method='GET' action='{{ $action }}' class='filter-bar' data-filter-form>{{ $slot }}<button class='btn btn-lokantara' type='submit'>Terapkan</button><a class='btn btn-light' href='{{ $action }}'>Reset</a></form>
