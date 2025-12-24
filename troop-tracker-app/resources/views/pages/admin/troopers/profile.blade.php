<div id="profile-form-container">

  <form method="POST"
        hx-trigger="submit"
        hx-post="{{ route('admin.troopers.profile-htmx', ['trooper'=>$trooper]) }}"
        hx-swap="outerHTML"
        hx-select="#profile-form-container"
        hx-target="#profile-form-container"
        hx-indicator="#transmission-bar-trooper"
        novalidate="novalidate">
    @csrf

    <x-input-container>
      <x-label>
        Display Name:
      </x-label>
      <x-input-text :property="'name'"
                    :value="$trooper->name" />
    </x-input-container>

    <x-input-container>
      <x-label>
        Email:
      </x-label>
      <x-input-text :property="'email'"
                    :value="$trooper->email" />
    </x-input-container>

    <x-input-container>
      <x-label>
        Phone (Optional):
      </x-label>
      <x-input-text :property="'phone'"
                    :value="$trooper->phone" />
    </x-input-container>

    <x-input-container>
      <x-label>
        Status:
      </x-label>
      <x-input-select :property="'membership_status'"
                      :options="\App\Enums\MembershipStatus::toArray()"
                      :value="$trooper->membership_status->value" />
    </x-input-container>

    <x-submit-container>
      <x-submit-button>
        Save
      </x-submit-button>
      <x-link-button-cancel :url="route('admin.troopers.list')" />
    </x-submit-container>

    <x-trooper-stamps :model="$trooper" />

  </form>
</div>