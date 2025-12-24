@extends('layouts.base')

@section('content')

<x-page-title>
  Dashboard
</x-page-title>

@include('pages.dashboard.overview')
@include('pages.dashboard.achievements', ['trooper_achievement'=>$trooper->trooper_achievement])
@include('pages.dashboard.organization-breakdown')
@include('pages.dashboard.costume-breakdown')

<!-- Navigation Tabs -->
<ul class="nav nav-tabs mb-4"
    role="tablist">
  <li class="nav-item">
    <a class="nav-link active"
       data-bs-toggle="tab"
       href="#upcoming">
      Upcoming Troops
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link"
       data-bs-toggle="tab"
       href="#history">
      Troop History
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link"
       data-bs-toggle="tab"
       href="#awards">
      Awards
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link"
       data-bs-toggle="tab"
       href="#photos">
      Tagged Photos
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link"
       data-bs-toggle="tab"
       href="#donations">
      Support Donations
    </a>
  </li>
</ul>

<!-- Tab Content -->
<div class="tab-content">

  <!-- Donations & Support -->
  <div class="tab-pane fade show active"
       id="upcoming">
    <x-card :label="'Upcoming Troops'">
      <div hx-get="{{ route('dashboard.upcoming-troops-htmx', ['trooper_id'=>$trooper->id]) }}"
           hx-trigger="load"
           hx-swap="outerHTML">
        <x-loading />
      </div>
    </x-card>
  </div>

  <!-- Troop History -->
  <div class="tab-pane fade"
       id="history">
    <x-card :label="'Troop History'">
      <div hx-get="{{ route('dashboard.historical-troops-htmx', ['trooper_id'=>$trooper->id]) }}"
           hx-trigger="load"
           hx-swap="outerHTML">
        <x-loading />
      </div>
    </x-card>
  </div>

  <!-- Awards -->
  <div class="tab-pane fade"
       id="awards">
    <x-card :label="'Awards'">
      <div hx-get="{{ route('dashboard.awards-htmx', ['trooper_id'=>$trooper->id]) }}"
           hx-trigger="load"
           hx-swap="outerHTML">
        <x-loading />
      </div>
    </x-card>
  </div>

  <!-- Tagged Photos -->
  <div class="tab-pane fade"
       id="photos">
    <x-card :label="'Tagged Photos'">
      <div hx-get="{{ route('dashboard.tagged-uploads-htmx', ['trooper_id'=>$trooper->id]) }}"
           hx-trigger="load"
           hx-swap="outerHTML">
        <x-loading />
      </div>
    </x-card>
  </div>

  <!-- Donations -->
  <div class="tab-pane fade"
       id="donations">
    <x-card :label="'Support Donations'">
      <div hx-get="{{ route('dashboard.donations-htmx', ['trooper_id'=>$trooper->id]) }}"
           hx-trigger="load"
           hx-swap="outerHTML">
        <x-loading />
      </div>
    </x-card>
  </div>

</div>

@endsection