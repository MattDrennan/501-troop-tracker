@props(['id' => 'tabs'])

<div>
  <ul class="nav nav-tabs d-none d-md-flex"
      id="{{ $id }}"
      role="tablist">
    {{ $slot }}
  </ul>
  <div class="tab-content mt-3">
    {{ $panes ?? '' }}
  </div>
</div>