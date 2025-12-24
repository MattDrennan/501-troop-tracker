<x-transmission-bar :id="'profile'" />

<div id="profile-form-container">
  <form method="POST"
        hx-trigger="submit"
        hx-post="{{ route('account.profile-htmx') }}"
        hx-swap="outerHTML"
        hx-select="#profile-form-container"
        hx-target="#profile-form-container"
        hx-indicator="#transmission-bar-profile"
        novalidate="novalidate">
    @csrf

    <x-input-container>
      <x-label>
        Display Name (first &amp last name, or use a nickname to remain anonymous):
      </x-label>
      <x-input-text :property="'name'"
                    :value="$name" />
    </x-input-container>

    <x-input-container>
      <x-label>
        Email:
      </x-label>
      <x-input-text :property="'email'"
                    :value="$email" />
    </x-input-container>

    <x-input-container>
      <x-label>
        Phone (Optional):
      </x-label>
      <x-input-text :property="'phone'"
                    :value="$phone" />
    </x-input-container>

    <x-submit-container>
      <span class="float-start">
        <a href="{{ route('dashboard.display') }}"
           class="btn btn-outline-info mb-2">
          View Your Dashboard
        </a>
      </span>
      <x-submit-button>
        Save
      </x-submit-button>
    </x-submit-container>
  </form>
</div>