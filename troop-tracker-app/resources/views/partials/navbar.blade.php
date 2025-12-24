<nav class="navbar navbar-dark navbar-expand-lg bg-black rounded-3 p-0">
  <div class="container-fluid justify-content-center">
    <!-- Hamburger toggle -->
    <button class="navbar-toggler ms-auto me-3 my-2"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#pillNav"
            aria-controls="pillNav"
            aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>


    <div class="collapse navbar-collapse justify-content-center"
         id="pillNav">
      <ul class="navbar-nav flex-wrap">
        <x-nav-link :href="'#'"
                    :active="request()->routeIs('home')">
          Home
        </x-nav-link>
        @if(config('tracker.forum.url') != '')
        <x-nav-link :href="config('tracker.forum.url')">
          Forum
        </x-nav-link>
        @endif
        <x-nav-link :href="route('faq')"
                    :active="request()->routeIs('faq')">
          FAQ
        </x-nav-link>

        @role(['administrator','moderator'])
        <x-nav-link :href="route('admin.display')"
                    :active="request()->routeIs('admin.*')">
          Command Staff
        </x-nav-link>
        @endrole

        @auth
        <x-nav-link :href="route('account.display')"
                    :active="request()->routeIs('account.*')">
          Manage Account
        </x-nav-link>
        <x-nav-link :href="route('auth.logout')">
          Logout
        </x-nav-link>
        @else
        <x-nav-link :href="route('auth.register')"
                    :active="request()->routeIs('auth.register')">
          Register
        </x-nav-link>
        <x-nav-link :href="route('auth.login')"
                    :active="request()->routeIs('auth.login')">
          Login
        </x-nav-link>
        @endauth
      </ul>
    </div>
  </div>
</nav>