@php($organization_selected = old("organizations.{$organization->id}.selected", $organization->selected))

<div id="organization-selection-{{ $organization->id }}">
  <x-input-container>
    <x-input-checkbox :property="'organizations.' . $organization->id . '.selected'"
                      :label="$organization->name"
                      :value="'1'"
                      :checked="$organization_selected"
                      :spinner="$organization->id"
                      data-organization-id="{{ $organization->id }}"
                      hx-post="{{ route('auth.register-htmx', ['organization'=>$organization->id]) }}"
                      hx-target="#organization-selection-{{ $organization->id }}"
                      hx-swap="outerHTML"
                      hx-trigger="change"
                      hx-include="closest div"
                      hx-indicator="#spinner-{{ $organization->id }}" />
  </x-input-container>

  @if($organization_selected)
  <div class="organization-{{ $organization->id }} ps-4">
    <x-input-container>
      <x-label>
        {{ $organization->identifier_display }}:
      </x-label>
      <x-input-text :property="'organizations.' . $organization->id . '.identifier'" />
    </x-input-container>
    @if($organization->organizations->count() > 0)
    <x-input-container>
      <x-input-select :property="'organizations.' . $organization->id . '.region_id'"
                      :options="$organization->organizations->pluck('name', 'id')->toArray()"
                      :placeholder="'-- Select your Region/Garrison --'"
                      :value="$organization->organizations->where('selected', true)->first()->id ?? null"
                      hx-post="{{ route('auth.register-htmx', ['organization'=>$organization->id]) }}"
                      hx-select="#unit-container-{{ $organization->id }}"
                      hx-target="#unit-container-{{ $organization->id }}"
                      hx-swap="outerHTML"
                      hx-trigger="change"
                      hx-include="closest div"
                      hx-indicator="#transmission-bar-register-organization" />
    </x-input-container>

    @php($rid = $organization->organizations->where('selected', true)->first()->id ?? null)
    @php($rid = old("organizations.{$organization->id}.region_id", $rid))
    @php($region = $organization->organizations->find($rid))

    <x-input-container id="unit-container-{{ $organization->id }}">
      @if($region && $region->organizations->count() > 0)
      <x-input-select :property="'organizations.' . $organization->id . '.unit_id'"
                      :options="$region->organizations->pluck('name', 'id')->toArray()"
                      :placeholder="'-- Select your Unit/Squad --'" />
      @endif
    </x-input-container>

    @endif
  </div>
  @endif
</div>