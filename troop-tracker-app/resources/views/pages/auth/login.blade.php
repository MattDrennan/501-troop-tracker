@extends('layouts.base')

@section('content')

<x-slim-container class="mt-4">
  <x-card :label="'Login'">

    <form method="POST"
          action="{{ route('auth.login') }}"
          novalidate="novalidate">
      @csrf

      <x-input-container>
        <x-label>
          @if(config('tracker.plugins.type') != 'standalone')
          {{ config('tracker.forum.name') }}
          @endif
          Username:
        </x-label>
        <x-input-text autofocus
                      :property="'username'" />
      </x-input-container>

      <x-input-container>
        <x-label>
          @if(config('tracker.plugins.type') != 'standalone')
          {{ config('tracker.forum.name') }}
          @endif
          Password:
        </x-label>
        <x-input-password :property="'password'" />
      </x-input-container>

      <x-input-container>
        <x-input-checkbox :property="'remember_me'"
                          :label="'Keep me logged in'"
                          :value="'Y'" />
      </x-input-container>

      <x-submit-container>
        <x-submit-button>
          Login
        </x-submit-button>
      </x-submit-container>
    </form>

    <p>
      <small>
        <b>Remember:</b><br />Login with your {{ config('tracker.forum.name') }} board username and password.
      </small>
    </p>
  </x-card>
</x-slim-container>

@endsection