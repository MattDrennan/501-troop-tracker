@props(['property', 'value'=>''])
@php($bracketed = to_bracket_name($property))
<input type="hidden"
       name="{{ $bracketed }}"
       id="{{ $property }}"
       value="{{ old($property, $value) }}" />