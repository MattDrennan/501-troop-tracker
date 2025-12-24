<x-card :label="'Troop Breakdown by Costume'">
  <div class="container">
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
      @forelse($total_troops_by_costume as $costume)
      <div class="col">
        <span class="badge bg-dark w-100 px-2 py-2 text-truncate"
              style="max-width: 100%;"
              title="{{ $costume->name }}">
          <span class="d-flex justify-content-between w-100">
            <span class="text-truncate">
              {{ $costume->name }}
            </span>
            <span class="fw-bold">
              @if($costume->troop_count > 0)
              <x-number-format :value="$costume->troop_count"
                               :prefix="'#'" />
              @else
              <span class="text-muted">
                N/A
              </span>
              @endif
            </span>
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