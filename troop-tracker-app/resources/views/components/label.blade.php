@props(['value', 'optional'=>false])

<label {{$attributes->merge(['class'=>'form-label'])}}>
  {{ $value ?? $slot }}
</label>