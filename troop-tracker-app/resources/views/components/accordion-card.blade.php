@props(['id'=>'card-' . uniqid(), 'label', 'open'=>false, 'danger'=>false])

<div class="card mb-3 {{ $danger ? 'border-danger' : '' }}">
  <div class="card-header d-flex justify-content-between align-items-center"
       data-bs-toggle="collapse"
       data-bs-target="#{{ $id }}"
       role="button">
    {{ $label }}
    <i class="fa-solid fa-chevron-{{ $open ? 'down' : 'up'}} collapse-icon"></i>
  </div>
  <div id="{{ $id }}"
       class="collapse {{ $open ? 'show' : '' }}">
    <div class="card-body">
      {{ $slot }}
    </div>
  </div>
</div>