@extends('layouts.base')

@section('content')

<x-transmission-bar :id="'organization'" />

<x-slim-container>

  <x-card :label="'Update '. $organization->type->name">
    <form method="POST"
          novalidate="novalidate">
      @csrf

      @isset($organization->parent)
      <x-input-container>
        <x-label>
          Parent {{ $organization->parent->type->name }}:
        </x-label>
        <x-input-hidden :property="'parent_id'"
                        :value="$organization->parent_id" />
        <x-input-text :property="'parent_name'"
                      :disabled="true"
                      :value="$organization->parent->name" />
      </x-input-container>
      @endisset

      @if($organization->type == \App\Enums\OrganizationType::Unit)
      @can('update', $organization->parent)
      {{-- TODO MOVE PARENTS --}}
      @endcan
      @endif

      <x-input-container>
        <x-label>
          Name:
        </x-label>
        <x-input-text :property="'name'"
                      :value="$organization->name" />
      </x-input-container>

      <x-submit-container>
        <x-submit-button>
          Save
        </x-submit-button>
        <x-link-button-cancel :url="route('admin.organizations.list')" />
      </x-submit-container>

      <x-trooper-stamps :model="$organization" />
    </form>
  </x-card>

</x-slim-container>

@endsection