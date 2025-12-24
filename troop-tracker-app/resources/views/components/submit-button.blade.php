@props(['uid'=>'button-' . uniqid()])

<button data-id="{{ $uid }}"
        data-action="htmx-disable"
        hx-headers='{"X-Dispatch-ID": "{{ $uid }}"}'
        {{$attributes->merge(['type'=>'submit', 'class'=>'btn btn-primary'])}}>
  {{ $slot }}
</button>