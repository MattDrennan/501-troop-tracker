@props(['href'=>'#', 'active'=>false])

<li class="nav-item">
  <a class="nav-link text-white px-4 py-3 {{ $active ? 'active' : '' }}"
     href="{{ $href }}">
    {{ $slot }}
  </a>
</li>