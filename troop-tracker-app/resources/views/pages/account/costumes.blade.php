<x-transmission-bar :id="'trooper-costumes'" />

<div id="trooper-costumes-form-container">

  <form method="POST"
        novalidate="novalidate">
    @csrf

    <x-input-container id="organization-select-container">
      @if($organizations->count() == 0)
      <x-message :type="'danger'">
        You do not have any assigned organizations
      </x-message>
      @else
      <x-input-select :property="'organization_id'"
                      :options="$organizations->pluck('name', 'id')->toArray()"
                      :value="$selected_organization->id ?? -1"
                      :placeholder="'-- Select your Organization --'"
                      hx-get="{{ route('account.costumes-htmx') }}"
                      hx-trigger="change"
                      hx-select="#costume-select-container"
                      hx-target="#costume-select-container"
                      hx-indicator="#transmission-bar-trooper-costumes" />
      @endif
    </x-input-container>

    <x-input-container id="costume-select-container">
      @if(!empty($selected_organization))
      <x-input-select :property="'costume_id'"
                      :options="$costumes"
                      :value="-1"
                      :placeholder="'-- Select your Costume --'"
                      hx-post="{{ route('account.costumes-htmx') }}"
                      hx-select="#trooper-costumes-table"
                      hx-target="#trooper-costumes-table"
                      hx-swap="outerHTML"
                      hx-indicator="#transmission-bar-trooper-costumes"
                      hx-include="closest form" />
      @endif
    </x-input-container>
  </form>

  <x-table id="trooper-costumes-table">
    <thead>
      <tr>
        <th>
          Costume
        </th>
        <th class="text-end">
          Remove
        </th>
      </tr>
    </thead>
    @forelse($trooper_costumes as $trooper_costume)
    <tr>
      <td>
        {{ $trooper_costume->fullCostumeName() }}
      </td>
      <td class="text-end">
        <x-button-delete hx-delete="{{ route('account.costumes-htmx', ['costume_id'=>$trooper_costume->id]) }}"
                         hx-select="#trooper-costumes-table"
                         hx-target="#trooper-costumes-table"
                         hx-swap="outerHTML"
                         hx-indicator="#transmission-bar-trooper-costumes" />
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="2">
        No Favorite Costumes ... Yet!
        <p class="text-muted">
          To add a favorite costume, select your organization/club, then simply select a costume to add
          to your profile.
        </p>
      </td>
    </tr>
    @endforelse
  </x-table>
</div>