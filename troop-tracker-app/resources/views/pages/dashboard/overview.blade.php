<!-- Profile Card -->
<x-card :label="'Overview'">

  <div class="row align-items-center">

    <!-- Column A: Image + Boards Link -->
    <div class="col-md-4 col-lg-3 text-center text-md-start align-self-start mb-3">
      @if(isset($image_url))
      <img src="#"
           class="img-fluid rounded mb-3"
           alt="Profile Picture">
      @endif
      <div>
        <a href="#"
           class="btn btn-outline-light w-100">
          Boards Profile
          <span class="fa fa-fw fa-external-link"></span>
        </a>
      </div>
    </div>

    <!-- Column B: Trooper Info -->
    <div class="col-md-8 col-lg-6 align-self-start">
      <h4 class="mb-2 text-upper">
        {{ $trooper->name }}
      </h4>
      <x-table>
        <tr>
          <th>Member Since:</th>
          <td>May 26, 2025</td>
        </tr>
        <tr>
          <th>Trooper Rank:</th>
          <td>
            @if(isset($trooper->trooper_rank))
            <x-number-format :value="$trooper->trooper_achievement->trooper_rank"
                             :prefix="'#'" />
            @else
            <span class="text-muted">
              N/A
            </span>
            @endif
          </td>
        </tr>
        <tr>
          <th>Volunteer Hours</th>
          <td><x-number-format :value="$trooper->trooper_achievement->volunteer_hours" /></td>
        </tr>
        <tr>
          <th>Direct Donations</th>
          <td><x-number-format :value="$trooper->trooper_achievement->direct_funds"
                             :prefix="'$'" /></td>
        </tr>
        <tr>
          <th>Indirect Donations</th>
          <td><x-number-format :value="$trooper->trooper_achievement->indirect_funds"
                             :prefix="'$'" /></td>
        </tr>
      </x-table>

    </div>

  </div>
</x-card>