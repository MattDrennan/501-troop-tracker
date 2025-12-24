@extends('layouts.base')

@section('content')

<x-table>
  <thead>
    <tr>
      <th>
        Award
      </th>
      <th class="text-center">
        &nbsp;
      </th>
      <th class="text-center">
        &nbsp;
      </th>
    </tr>
  </thead>
  @forelse($awards as $award)
  <tr>
    <td>
      {{ $award->name }}
    </td>
    <td class="text-center">
    </td>
    <td class="text-center">
    </td>
  </tr>
  @empty
  <tr>
    <td colspan="3">
      No Awards Defined ... Yet!
    </td>
  </tr>
  @endforelse
</x-table>
@endsection