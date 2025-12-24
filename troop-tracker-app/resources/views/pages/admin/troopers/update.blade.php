@extends('layouts.base')

@section('content')

<x-transmission-bar :id="'trooper'" />

<x-slim-container>

  <x-card :label="'Profile'">
    @include('pages.admin.troopers.profile',['trooper'=>$trooper])
  </x-card>

  @if($trooper->membership_role == \App\Enums\MembershipRole::Moderator)
  <x-card :label="'Authority'">
    @include('pages.admin.troopers.authority',['organization_authorities'=>$organization_authorities])
  </x-card>
  @endif

</x-slim-container>

@endsection

@section('page-script')
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', () => {
    function bindCascadeCheckboxes() {
      document.querySelectorAll('td.cascade input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function () {
          const isChecked = this.checked;
          const row = this.closest('tr');
          const id = row.dataset.id;

          // Cascade down: find all rows with data-parent = this row's id
          const children = document.querySelectorAll(`tr[data-parent-id="${id}"] input[type="checkbox"]`);
          children.forEach(childCb => {
            childCb.checked = isChecked;

            if (isChecked) {
              childCb.disabled = true;   // disable child if parent is checked
            } else {
              childCb.disabled = false;  // re-enable child if parent is unchecked
            }

            // recursively trigger change if you want deeper cascade
            childCb.dispatchEvent(new Event('change'));
          });
        });
      });
    }

    bindCascadeCheckboxes();
    document.body.addEventListener('htmx:afterSettle', bindCascadeCheckboxes);
  });
</script>
@endsection