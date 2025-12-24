@props(['label'=>'', 'danger'=>false])

<div class="card mb-3 {{ $danger ? 'border-danger' : '' }}">
  @if(!empty($label))
  <div class="card-header d-flex justify-content-between align-items-center">
    {{ $label }}
  </div>
  @endif
  <div class="card-body">
    {{ $slot }}
  </div>
</div>