@props(['url'])

<a class="btn btn-sm btn-outline-danger float-end"
   href="{{ $url }}">
  <i class="fa fa-fw fa-times"></i>
  {{ $slot }}
</a>