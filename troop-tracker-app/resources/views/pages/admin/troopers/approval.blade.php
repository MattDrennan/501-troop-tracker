<div id="trooper-approval-{{ $trooper->id }}"
     class="card h-100 shadow-sm">
  <div class="card-header text-uppercase">
    {{ $trooper->name }}
  </div>
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-4">Email:</dt>
      <dd class="col-8">{{ $trooper->email }}</dd>
      <dt class="col-4">Phone:</dt>
      <dd class="col-8">{{ $trooper->phone ?? 'n/a' }}</dd>
      <dt class="col-4">Role:</dt>
      <dd class="col-8">{{ $trooper->membership_role }}</dd>
    </dl>
    <hr />
    <x-table>
      <thead>
        <tr>
          <th>Organization</th>
          <th>Identifier</th>
        </tr>
      </thead>
      @foreach($trooper->organizations as $organization)
      <tr>
        <td>{{ $organization->name }}</td>
        <td>{{ $organization->pivot->identifier }}</td>
      </tr>
      @endforeach
      <thead>
        <tr>
          <th>Organization</th>
          <th>Selected Role</th>
        </tr>
      </thead>
      @foreach($trooper->trooper_assignments->filter(fn($a) => is_null($a->organization->parent_id)) as $org_asg)
      <tr>
        <td class="ps-{{ $org_asg->organization->depth }}">
          {{ $org_asg->organization->name }}
        </td>
        <td>{{ $org_asg->membership_role }}</td>
      </tr>
      @foreach($trooper->trooper_assignments->filter(fn($a) => $a->organization->parent_id == $org_asg->organization_id) as $reg_asg)
      <tr>
        <td class="ps-{{ $reg_asg->organization->depth }}">
          <i class="fa fa-fw fa-caret-right"></i>
          {{ $reg_asg->organization->name }}
        </td>
        <td>{{ $reg_asg->membership_role }}</td>
      </tr>
      @foreach($trooper->trooper_assignments->filter(fn($a) => $a->organization->parent_id == $reg_asg->organization_id) as $unit_asg)
      <tr>
        <td class="ps-{{ $unit_asg->organization->depth }}">
          <i class="fa fa-fw fa-caret-right"></i>
          <i class="fa fa-fw fa-caret-right"></i>
          {{ $unit_asg->organization->name }}
        </td>
        <td>{{ $unit_asg->membership_role }}</td>
      </tr>
      @endforeach
      @endforeach
      @endforeach
    </x-table>

  </div>
  <div class="card-footer d-flex justify-content-between">
    @if($trooper->isActive())
    <div class="w-100">
      <x-message type="success"
                 icon="fa-brands fa-empire"
                 class="w-100">
        Let the Trooping begin!
      </x-message>
    </div>
    @elseif($trooper->isDenied())
    <div class="w-100">
      <x-message type="danger">
        Denied Trooper Status
      </x-message>
    </div>
    @else
    <button class="btn btn-danger btn-sm"
            type="button"
            hx-post="{{ route('admin.troopers.deny-htmx', ['trooper' => $trooper]) }}"
            hx-swap="outerHTML"
            hx-select="#trooper-approval-{{ $trooper->id }}"
            hx-target="#trooper-approval-{{ $trooper->id }}"
            hx-indicator="#transmission-bar-approvals">
      Deny
    </button>
    <button class="btn btn-success btn-sm"
            type="button"
            hx-post="{{ route('admin.troopers.approve-htmx', ['trooper' => $trooper]) }}"
            hx-swap="outerHTML"
            hx-select="#trooper-approval-{{ $trooper->id }}"
            hx-target="#trooper-approval-{{ $trooper->id }}"
            hx-indicator="#transmission-bar-approvals">
      Approve
    </button>
    @endif
  </div>
</div>