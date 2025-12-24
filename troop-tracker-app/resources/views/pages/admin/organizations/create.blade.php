@extends('layouts.base')

@section('content')

<x-transmission-bar :id="'organization'" />

<x-slim-container>

  <x-card :label="'Create ' . $organization->type->name">
    <form method="POST"
          novalidate="novalidate">
      @csrf

      <x-input-container>
        <x-label>
          Parent:
        </x-label>
        <x-input-text :property="'parent_name'"
                      :disabled="true"
                      :value="$parent->name" />
      </x-input-container>

      <x-input-container>
        <x-label>
          Name:
        </x-label>
        <x-input-text :property="'name'"
                      :value="$organization->name" />
      </x-input-container>

      <x-submit-container>
        <x-submit-button>
          Create
        </x-submit-button>
        <x-link-button-cancel :url="route('admin.organizations.list')" />
      </x-submit-container>

    </form>
  </x-card>

</x-slim-container>

@endsection