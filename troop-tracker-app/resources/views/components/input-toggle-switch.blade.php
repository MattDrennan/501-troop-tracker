@props(['property', 'label'=>null, 'disabled'=>false, 'value'=>'1', 'checked'=>false])
@php
$haserror = $errors->has($property);
$bracketed = to_bracket_name($property);
@endphp
<div class="form-check form-switch">
  <input type="checkbox"
         name="{{ $bracketed }}"
         id="{{ $property }}"
         value="{{ $value }}"
         @checked($checked)
         @disabled($disabled)
         {{$attributes->merge(['class'=>'form-check-input' . ($haserror ? ' is-invalid' : '')])}} />
  @if($label)
  <label class="form-check-label"
         for="{{ $property }}">
    {{ $label }}
  </label>
  @endif
</div>