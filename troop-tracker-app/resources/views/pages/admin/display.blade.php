@extends('layouts.base')

@section('content')

<x-page-title>
  Command Staff
</x-page-title>

<x-dashboard-cards>
  <x-dashboard-card :label="'Events'"
                    :icon="'fa-calendar-days'"
                    :url="'#'">
    <p>
      Create, Update, and Manage Events
    </p>
  </x-dashboard-card>
  <x-dashboard-card :label="'Awards'"
                    :icon="'fa-award'"
                    :url="route('admin.awards.display')">
    <p>
      Create, Update Awards, as well as assign them to troopers
    </p>
  </x-dashboard-card>
  <x-dashboard-card :label="'Troopers'"
                    :icon="'fa-users-gear'"
                    :url="$not_approved > 0 ? route('admin.troopers.approvals') : '#'">
    @if($not_approved)
    <p class="text-warning">
      {{ $not_approved }} awaiting approval
    </p>
    @else
    Approve and Manage Troopers
    @endif
  </x-dashboard-card>
  <x-dashboard-card :label="'Bulletin Board'"
                    :icon="'fa-message'"
                    :url="route('admin.notices.list')">
    <p>
      Manage Site Messages
    </p>
  </x-dashboard-card>
  <x-dashboard-card :label="'Organizations'"
                    :icon="'fa-wrench'"
                    :url="route('admin.organizations.list')">
    <p>
      Create, Update, and Manage Organizations, Regions, Units
    </p>
  </x-dashboard-card>
  @can('viewAny', \App\Models\Setting::class)
  <x-dashboard-card :label="'Site Settings'"
                    :icon="'fa-wrench'"
                    :url="route('admin.settings.list')">
    <p>
      Manage Site Settings
    </p>
  </x-dashboard-card>
  @endcan
</x-dashboard-cards>

@endsection

@section('page-script')
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.card[data-route]');
    cards.forEach(function (card) {
      card.addEventListener('click', function () {
        const route = card.getAttribute('data-route');
        if (route) {
          window.location.href = route;
        }
      });
    });
  });
</script>
@endsection