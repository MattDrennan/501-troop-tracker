@extends('layouts.base')

@section('content')

<x-message>
  <b>New to the 501st and/or {{ config('tracker.forum.name') }}?</b> Or are you solely a member of another organization?
  Use this form below to start signing up for troops.
  <p>
    <i>Command Staff will need to approve your account prior to use.</i>
  </p>
</x-message>

<x-slim-container class="mt-4">

  <x-card :label="'Register with the Troop Tracker'">

    <form action="{{ route('auth.register') }}"
          method="POST"
          novalidate="novalidate">
      @csrf

      <x-input-container>
        <x-label>
          Display Name (first &amp; last name, or use a nickname):
        </x-label>
        <x-input-text autofocus
                      :property="'name'" />
      </x-input-container>

      <x-input-container>
        <x-label>
          Email:
        </x-label>
        <x-input-text :property="'email'" />
      </x-input-container>

      <x-input-container>
        <x-label>
          Phone (Optional):
        </x-label>
        <x-input-text :property="'phone'" />
      </x-input-container>

      <x-input-container>
        <x-label>
          @if (config('tracker.plugins.type') != 'standalone')
          {{ config('tracker.forum.name') }}
          @endif
          Username:
        </x-label>
        <x-input-text :property="'username'" />
      </x-input-container>

      <x-input-container>
        <x-label>
          @if (config('tracker.plugins.type') != 'standalone')
          {{ config('tracker.forum.name') }}
          @endif
          Password:
        </x-label>
        <x-input-password :property="'password'" />
      </x-input-container>

      <x-input-container>
        <x-label>
          Account Type:
        </x-label>
        <x-input-select :property="'account_type'"
                        :options="['member'=>'Member', 'handler'=>'Handler']"
                        :placeholder="'-- Select your Account Type --'" />
        <x-input-help>
          Are you a member of an organization selected below, or
          would you like to be assigned as a handler to an organization?
        </x-input-help>
      </x-input-container>


      <p>
        Select your associated organizations below.
      </p>

      <x-transmission-bar :id="'register-organization'" />

      @foreach ($organizations as $organization)
      @include('pages.auth.organization-selection', ['organization'=>$organization])
      @endforeach

      <x-submit-container>
        <x-submit-button>
          Register
        </x-submit-button>
      </x-submit-container>
      <br />

    </form>
  </x-card>
</x-slim-container>
@endsection