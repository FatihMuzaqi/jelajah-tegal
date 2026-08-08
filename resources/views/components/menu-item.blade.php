@props(['item'])
@php($href=isset($item['route'])?route($item['route']):($item['href']??'#'))
@php($active=isset($item['route'])&&request()->routeIs($item['route']))
<a href='{{ $href }}' class='sidebar-link {{ $active ? 'active' : '' }}' @if($active) aria-current='page' @endif><span class='menu-icon' aria-hidden='true'>{{ strtoupper(substr($item['label'],0,1)) }}</span><span class='menu-label'>{{ $item['label'] }}</span></a>
