@props(['label'=>'', 'url'=>'#', 'icon'=>''])
<div class="col">
  <div class="card h-100"
       data-route="{{ $url }}">
    <div class="card-header">
      <i class="text-white fa fa-fw {{ $icon }} me-2"></i>
      {{ $label }}
    </div>
    <div class="card-body">
      <p class="card-text">
        {{ $slot }}
      </p>
    </div>
  </div>
</div>