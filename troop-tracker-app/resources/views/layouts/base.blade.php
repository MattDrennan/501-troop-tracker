<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <!-- Meta Data -->
  <meta charset="UTF-8" />
  <meta name="viewport"
        content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible"
        content="ie=edge" />
  <meta name="csrf-token"
        content="{{ csrf_token() }}">

  <!-- Title -->
  <title>{{ config('tracker.name') }} - Troop Tracker</title>

  <link rel="icon"
        href="{{ url('img/favicon.png') }}"
        type="image/x-icon">

  @include('partials.styles')
</head>

<body class="bg-black d-flex flex-column min-vh-100">

  <div>
    <h1 class="text-center py-3 site-title">
      {{ config('tracker.name') }}
      <br />
      Troop Tracker
    </h1>
  </div>

  <div class="container rounded-3 shadow-sm p-4 main-content">
    @include('partials.navbar')
    @include('partials.bread-crumbs')
    @include('partials.messages')
    <div class="row dashboard-row"></div>

    @yield('content')

    @include('partials.footer')
  </div>

  @include('partials.scripts')
  @yield('page-script')
  @stack('scripts')

</body>

</html>