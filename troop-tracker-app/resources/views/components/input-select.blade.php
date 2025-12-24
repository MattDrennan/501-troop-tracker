@props(['disabled'=>false,'options'=>[],'placeholder'=>false,'optional'=>false,'property' =>'','value'=>null])
@php
$haserror = $errors->has($property);
$selected_value = old($property, $value);
$bracketed = to_bracket_name( $property);
@endphp
<select name="{{ $bracketed }}"
        id="{{ $property }}"
        @disabled($disabled)
        {{$attributes->merge(['class'=>'form-select' . ($haserror ? ' is-invalid' : '')])}}>
  {{ $slot }}
  @if($optional)
  <option value=""></option>
  @endif
  @if($placeholder)
  <option value="">{{ $placeholder }}</option>
  @endif
  @foreach ($options as $key=>$option)
  <option value="{{ $key }}"
          @selected($key==$selected_value)>
    {{ $option }}
  </option>
  @endforeach
</select>
<x-input-error :property="$property" />