@props(['type'=>'info', 'icon'=>'fa-exclamation-circle'])

<div class="alert alert-{{$type}} text-center border border-{{$type}} rounded-2 py-3 px-4 my-2">
  <i class="fa fa-fw {{ $icon }}"></i>
  {{ $slot }}
</div>