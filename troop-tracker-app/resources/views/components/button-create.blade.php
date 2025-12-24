@props(['uid'=>'button-' . uniqid()])

<button type="button"
        {{$attributes->merge(['class'=>'btn btn-outline-success'])}}>
  <i class="fa fw fa-add"></i>
  {{ $slot }}
</button>