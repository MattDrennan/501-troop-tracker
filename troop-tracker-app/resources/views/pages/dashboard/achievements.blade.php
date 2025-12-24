<x-card :label="'Trooper Achievements'">
  <div class="container">
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">

      @if ($trooper_achievement->completed_all_squads)
      @include('pages.dashboard.achievement', ['icon'=>'fa-network-wired', 'title'=>'All Squads - Sector Sweep'])
      @endif

      @include('pages.dashboard.achievement', ['icon'=>'fa-user-plus', 'title'=>'Initiated - Trooper Status Achieved'])

      @if ($trooper_achievement->first_troop_completed)
      @include('pages.dashboard.achievement', ['icon'=>'fa-flag-checkered', 'title'=>'1 Troop - Mission Initiated'])
      @endif

      @if ($trooper_achievement->trooped_10)
      @include('pages.dashboard.achievement', ['icon'=>'fa-shield-halved', 'title'=>'10 Troops - Outer Rim'])
      @endif

      @if ($trooper_achievement->trooped_25)
      @include('pages.dashboard.achievement', ['icon'=>'fa-user-shield', 'title'=>'25 Troops - Garrison Guard'])
      @endif

      @if ($trooper_achievement->trooped_50)
      @include('pages.dashboard.achievement', ['icon'=>'fa-medal', 'title'=>'50 Troops - Service Medal'])
      @endif

      @if ($trooper_achievement->trooped_75)
      @include('pages.dashboard.achievement', ['icon'=>'fa-star-half-stroke', 'title'=>'75 Troops - Rising Star'])
      @endif

      @if ($trooper_achievement->trooped_100)
      @include('pages.dashboard.achievement', ['icon'=>'fa-star', 'title'=>'100 Troops - Centurion Crest'])
      @endif

      @if ($trooper_achievement->trooped_150)
      @include('pages.dashboard.achievement', ['icon'=>'fa-trophy', 'title'=>'150 Troops - Campaign Captain'])
      @endif

      @if ($trooper_achievement->trooped_200)
      @include('pages.dashboard.achievement', ['icon'=>'fa-helmet-safety', 'title'=>'200 Troops - Elite Status'])
      @endif

      @if ($trooper_achievement->trooped_250)
      @include('pages.dashboard.achievement', ['icon'=>'fa-award', 'title'=>'250 Troops - Command Honor'])
      @endif

      @if ($trooper_achievement->trooped_300)
      @include('pages.dashboard.achievement', ['icon'=>'fa-certificate', 'title'=>'300 Troops - Doctrine Seal'])
      @endif

      @if ($trooper_achievement->trooped_400)
      @include('pages.dashboard.achievement', ['icon'=>'fa-crown', 'title'=>'400 Troops - Core Crown'])
      @endif

      @if ($trooper_achievement->trooped_500)
      @include('pages.dashboard.achievement', ['icon'=>'fa-gem', 'title'=>'500 Troops - Kyber Gem'])
      @endif

      @if ($trooper_achievement->trooped_501)
      @include('pages.dashboard.achievement', ['icon'=>'fa-brands fa-empire', 'title'=>'501 Troops - Vader\'s Fist'])
      @endif

    </div>
  </div>
</x-card>