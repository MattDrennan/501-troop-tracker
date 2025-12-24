<x-card :label="'Troop Breakdown by Organization'">
  <div class="container">
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
      @forelse($total_troops_by_organization as $organization)
      <div class="col">
        <span class="badge bg-dark d-flex justify-content-between align-items-center w-100 px-2 py-2">
          <span>
            {{ $organization->name }}
          </span>
          <span class="text-end fw-bold">
            @if($organization->troop_count > 0)
            <x-number-format :value="$organization->troop_count"
                             :prefix="'#'" />
            @else
            <span class="text-muted">
              N/A
            </span>
            @endif
          </span>
        </span>
      </div>
      @empty
      <span class="badge bg-dark d-inline-flex align-items-center gap-2">
        No Troops ... Yet!
      </span>
      @endforelse
    </div>
  </div>
</x-card>