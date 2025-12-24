@props(['organization'=>null, 'costume'=>null])
<span>
  @if(isset($organization))
  ( {{ $organization }} )
  @endif
  {{ $costume }}
</span>