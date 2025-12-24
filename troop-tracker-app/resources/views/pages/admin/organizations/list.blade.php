@extends('layouts.base')

@section('content')

<x-table>
  <thead>
    <tr>
      <th>
        Name
      </th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($organizations as $organization)
    <tr>
      <td>
        @foreach(range(0, $organization->depth - 1) as $i)
        <i class="fa fa-fw"></i>
        @endforeach
        {{ $organization->name }}
      </td>
      <td>
        <x-action-menu>
          @can('create', \App\Models\Organization::class)
          @if($organization->type != \App\Enums\OrganizationType::Unit)
          <x-action-link-create :url="route('admin.organizations.create', ['parent'=>$organization])" />
          @endif
          @endcan
          @if(Auth::user()->isAdministrator() || $organization->trooper_assignments->count() > 0)
          <x-action-link-update :url="route('admin.organizations.update', ['organization'=>$organization])" />
          @endif
          <x-action-separator />
          @if(Auth::user()->isAdministrator() || $organization->trooper_assignments->count() > 0)
          <x-action-link :label="'Add Notice'"
                         :icon="'fa-add'"
                         :url="route('admin.notices.create', ['organization_id'=>$organization->id])" />
          <x-action-link :label="'Notices'"
                         :url="route('admin.notices.list', ['organization_id'=>$organization->id])" />
          @endif
        </x-action-menu>
      </td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">
        {{ $organizations->count() }} Organizations
      </td>
    </tr>
  </tfoot>
</x-table>

@endsection