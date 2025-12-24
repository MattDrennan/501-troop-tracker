@extends('layouts.base')

@section('content')

<x-page-title>
  Manage Account
</x-page-title>

<x-slim-container>

  <!-- Profile Information -->
  <x-card :label="'Profile Information'">
    @include('pages.account.profile', $trooper->only('name', 'email', 'phone'))
  </x-card>

  <!-- Notification Settings -->
  <x-card :label="'Notification Settings'">
    <div hx-get="{{ route('account.notifications-htmx') }}"
         hx-trigger="load"
         hx-swap="outerHTML">
      <x-loading />
    </div>
  </x-card>

  <!-- Trooper Costumes -->
  <x-card :label="'Trooper Costumes'">
    <div hx-get="{{ route('account.costumes-htmx') }}"
         hx-trigger="load"
         hx-swap="outerHTML">
      <x-loading />
    </div>
  </x-card>

  <!-- Donations & Support -->
  <x-card :label="'Donations & Support'">
    <div hx-get="{{ route('support-htmx') }}"
         hx-trigger="load"
         hx-swap="outerHTML">
      <x-loading />
    </div>
  </x-card>

</x-slim-container>

{{--
<!-- Danger Zone -->
<x-card :label="'Danger Zone'"
        :danger="true">
  <button class="btn btn-warning mb-2">Change Password</button><br>
  <button class="btn btn-danger">Deactivate Account</button>
</x-card>
--}}
@endsection

@section('page-script')
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', () => {
    function bindCascadeCheckboxes() {
      document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
          const isChecked = this.checked;
          const name = this.getAttribute('name');

          // Organization-level toggle
          if (name.startsWith('organizations') && this.hasAttribute('data-organization-id')) {
            const orgId = this.getAttribute('data-organization-id');
            document.querySelectorAll(`input[type="checkbox"][data-organization-id="${orgId}"]`).forEach(cb => {
              if (cb !== this) cb.checked = isChecked;
            });
          }

          // Region-level toggle
          if (name.startsWith('regions') && this.hasAttribute('data-region-id')) {
            const regionId = this.getAttribute('data-region-id');
            document.querySelectorAll(`input[type="checkbox"][data-region-id="${regionId}"]`).forEach(cb => {
              if (cb !== this) cb.checked = isChecked;
            });
          }
        });
      });
    }

    // Initial bind
    bindCascadeCheckboxes();

    // Re-bind after HTMX swaps content
    document.body.addEventListener('htmx:afterSettle', bindCascadeCheckboxes);
  });
</script>
@endsection