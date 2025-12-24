@props(['uid'=>'button-' . uniqid()])

<button type="button"
        {{$attributes->merge(['class'=>'btn btn-outline-danger'])}}>
  <i class="fa fw fa-times"></i>
  {{ $slot }}
</button>