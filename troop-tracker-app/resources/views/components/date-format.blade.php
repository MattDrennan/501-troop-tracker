@props(['value'=>null, 'format'=>'Y-m-d'])
@php
if (is_string($value)) $value = \Carbon\Carbon::parse($value);
@endphp
<span>
  @if($value)
  {{ $value->format($format) }}
  @else
  -
  @endif
</span>