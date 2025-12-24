<ul class="list-group list-group-flush">
  @foreach($organizations as $organization)
  <li class="list-group-item pointer"
      data-property="{{ $property }}"
      data-id="{{ $organization->id }}"
      data-name="{{ $organization->name }}">
    @foreach(range(0, $organization->depth - 1) as $i)
    <i class="fa fa-fw"></i>
    @endforeach
    {{ $organization->name }}
  </li>
  @endforeach
</ul>