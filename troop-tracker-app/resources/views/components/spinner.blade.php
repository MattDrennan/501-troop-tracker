@props(['id'=>'x-' . uniqid()])

<span id="spinner-{{ $id }}"
      class="htmx-indicator"
      style="margin-left: 8px;">
  <i class="fa fa-spinner fa-spin"></i>
</span>