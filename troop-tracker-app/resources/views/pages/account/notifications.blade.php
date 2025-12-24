<x-transmission-bar :id="'notifications'" />

<div id="notifications-form-container">

  <form method="POST"
        hx-trigger="submit"
        hx-post="{{ route('account.notifications-htmx') }}"
        hx-swap="outerHTML"
        hx-select="#notifications-form-container"
        hx-target="#notifications-form-container"
        hx-indicator="#transmission-bar-notifications"
        novalidate="novalidate">
    @csrf

    <h3>Website</h3>

    <x-input-container class="ps-5">
      <!-- efast -->
      <x-input-checkbox :property="'instant_notification'"
                        :label="'Instant Event Notification'"
                        :value="1"
                        :checked="$instant_notification" />
    </x-input-container>

    <x-input-container class="ps-5">
      <!-- econfirm -->
      <x-input-checkbox :property="'attendance_notification'"
                        :label="'Confirm Attendance Notification'"
                        :value="1"
                        :checked="$attendance_notification" />
    </x-input-container>

    <x-input-container class="ps-5">
      <!-- ecommandnotify -->
      <x-input-checkbox :property="'command_staff_notification'"
                        :label="'Command Staff Notifications'"
                        :value="1"
                        :checked="$command_staff_notification" />
    </x-input-container>

    <h3>Squads / Clubs</h3>
    <p>
      <i>Note: Events are categorized by 501st region territory. To receive event notifications for a particular area,
        ensure you subscribed to the appropriate region(s). Organization notifications are used in command staff e-mails, to send
        command staff information on trooper milestones based on region or organization.</i>
    </p>

    @foreach ($organizations as $organization)
    <x-input-container class="ps-5">
      <x-input-checkbox :property="'organizations.' . $organization->id . '.notification'"
                        :label="$organization->name"
                        :value="1"
                        :checked="$organization->selected"
                        data-organization-id="{{ $organization->id }}" />
      @foreach ($organization->organizations as $region)
      <x-input-container class="ps-5">
        <x-input-checkbox :property="'regions.' . $region->id . '.notification'"
                          :label="$region->name"
                          :value="1"
                          :checked="$region->selected"
                          data-organization-id="{{ $organization->id }}"
                          data-region-id="{{ $region->id }}" />
        @foreach ($region->organizations as $unit)
        <x-input-container class="ps-5">
          <x-input-checkbox :property="'units.' . $unit->id . '.notification'"
                            :label="$unit->name"
                            :value="1"
                            :checked="$unit->selected"
                            data-organization-id="{{ $organization->id }}"
                            data-region-id="{{ $region->id }}" />
        </x-input-container>
        @endforeach
      </x-input-container>
      @endforeach
    </x-input-container>
    @endforeach

    <x-submit-container>
      <x-submit-button>
        Save
      </x-submit-button>
    </x-submit-container>
  </form>
</div>