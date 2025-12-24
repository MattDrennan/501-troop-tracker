@props(['label','target' => null,'href' => null,'active' => false])

<li class="nav-item"
    role="presentation">
  <a class="nav-link {{ $active ? 'active' : '' }}"
     href="{{ $target }}"
     data-bs-toggle="tab"
     role="tab">
    {{ $label }}
  </a>
</li>