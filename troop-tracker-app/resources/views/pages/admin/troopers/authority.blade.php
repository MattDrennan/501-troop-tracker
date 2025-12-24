<div id="authority-form-container">

  <form method="POST"
        hx-trigger="submit"
        hx-post="{{ route('admin.troopers.authority-htmx', ['trooper'=>$trooper]) }}"
        hx-swap="outerHTML"
        hx-select="#authority-form-container"
        hx-target="#authority-form-container"
        hx-indicator="#transmission-bar-trooper"
        novalidate="novalidate">
    @csrf

    <x-input-container>
      <x-label>
        Role:
      </x-label>
      <x-input-select :property="'membership_role'"
                      :options="\App\Enums\MembershipRole::toArray()"
                      :value="$trooper->membership_role->value" />
      <x-input-help>
        If selected as {{ \App\Enums\MembershipRole::Administrator->name }}, they have full control within the Command Staff.
        If selected as {{ \App\Enums\MembershipRole::Moderator->name }}, they have full control over their
        assigned organizations as {{ \App\Enums\MembershipRole::Moderator->name }}. A {{ \App\Enums\MembershipRole::Member->name }},
        can sign up for events provided they are {{ \App\Enums\MembershipStatus::Active->name }},
      </x-input-help>
    </x-input-container>

    <x-submit-container>
      <x-submit-button>
        Save
      </x-submit-button>
      <x-link-button-cancel :url="route('admin.troopers.list')" />
    </x-submit-container>

    <x-table>
      <thead>
        <tr>
          <th>Assignment</th>
          <th>Moderator</th>
          <th class="text-center">Notify</th>
          <th class="text-center">Member</th>
        </tr>
      </thead>
      @php($selected_map = [])
      @foreach ($organization_authorities as $organization)

      @php($trooper_assignment = $organization->trooper_assignments->first())
      @php($parent_selected = $selected_map[$organization->parent_id] ?? false)

      <tr data-id="{{ $organization->id }}"
          data-parent-id="{{ $organization->parent_id }}">
        <td>
          @foreach(range(0, $organization->depth - 1) as $i)
          <i class="fa fa-fw"></i>
          @endforeach
          <label for="{{'moderators.'.$organization->id.'.selected'}}">
            {{ $organization->name }}
          </label>
        </td>
        <td class="cascade">
          <x-input-checkbox :property="'moderators.'.$organization->id.'.selected'"
                            :checked="$parent_selected || ($trooper_assignment->moderator ?? false)"
                            :disabled="$parent_selected" />
        </td>
        <td class="text-center">
          <x-yes-no :value="$trooper_assignment->notify ?? false" />
        </td>
        <td class="text-center">
          <x-yes-no :value="$trooper_assignment->member ?? false" />
        </td>
      </tr>
      @php($selected_map[$organization->id] = $trooper_assignment->moderator ?? false)
      @endforeach
    </x-table>

    <x-submit-container>
      <x-submit-button>
        Save
      </x-submit-button>
      <x-link-button-cancel :url="route('admin.troopers.list')" />
    </x-submit-container>
  </form>

</div>