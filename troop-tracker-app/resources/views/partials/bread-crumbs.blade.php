@inject ('crumbs', 'App\Services\BreadCrumbService')

@if ($crumbs->hasCrumbs())
<div>
  <nav class="mt-1">
    <ol class="breadcrumb border border-0">
      @foreach($crumbs->getCrumbs() as $crumb)
      <li class="breadcrumb-item">
        @if(empty($crumb->url))
        {{ $crumb->title }}
        @else
        <a href="{{ $crumb->url }}">{{ $crumb->title }}</a>
        @endif
      </li>
      @endforeach
    </ol>
  </nav>
</div>
@endif