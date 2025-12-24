@props(['value'=>0, 'decimals'=>0, 'prefix'=>null])

<span>
  @if(isset($prefix))
  {{ $prefix }}
  @endif
  {{ $value == 0 ? '-' : number_format($value, $decimals) }}
</span>