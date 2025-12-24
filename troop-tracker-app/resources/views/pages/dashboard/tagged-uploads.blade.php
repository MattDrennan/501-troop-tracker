<div class="row g-3">
  @foreach ($uploads as $upload)
  <div class="col-6 col-md-4 col-lg-3">
    <div class="card h-100">
      <div class="ratio ratio-1x1">
        <img src="{{ $upload->filename }}"
             class="img-fluid"
             style="object-fit: cover;" />
      </div>
    </div>
  </div>
  @endforeach
</div>