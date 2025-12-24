@props(['label','url','icon'=>'fa-rectangle-list'])

<li>
  <a class="dropdown-item"
     href="{{ $url }}">
    <i class="fa fa-fw {{ $icon }} me-3"></i>
    {{ $label ?? $slot }}
  </a>
</li>