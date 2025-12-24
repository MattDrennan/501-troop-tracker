@extends('layouts.base')

@section('content')

<x-table>
  <thead>
    <tr>
      <th>
        Name
      </th>
      <th>
        Organization
      </th>
      <th>
        <x-link-button-create :url="route('admin.notices.create', ['organization_id'=> request('organization_id')])">
          Notice
        </x-link-button-create>
      </th>
    </tr>
  </thead>
  <tbody>
    @foreach($notices as $notice)
    <tr>
      <td>
        {{ $notice->title }}
        <i class="fa fa-fw fa-circle text-{{ $notice->type->value }} me-2"></i>
      </td>
      <td>
        {{ $notice->Organization->name ?? '-' }}
      </td>
      <td>
        <x-action-menu>
          @if(Auth::user()->isAdministrator() && $notice->organization == null)
          <x-action-link-update :url="route('admin.notices.update', ['notice'=>$notice])" />
          @elseif($notice->organization->trooper_assignments->count() > 0)
          <x-action-link-update :url="route('admin.notices.update', ['notice'=>$notice])" />
          @endif
        </x-action-menu>
      </td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3">
        {{ $notices->count() }} Notices
      </td>
    </tr>
  </tfoot>
</x-table>

@endsection