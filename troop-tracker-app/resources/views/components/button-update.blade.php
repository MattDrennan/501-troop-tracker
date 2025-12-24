@props(['uid'=>'button-' . uniqid()])

<button type="button"
        {{$attributes->merge(['class'=>'btn btn-outline-primary'])}}>
  <i class="fa fw fa-edit"></i>
  {{ $slot }}
</button>