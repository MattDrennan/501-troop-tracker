@props(['url'])

<a class="btn btn-sm btn-outline-warning float-end"
   href="{{ $url }}">
  <i class="fa fa-fw fa-edit"></i>
  {{ $slot }}
</a>