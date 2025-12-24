<x-table>
  <thead>
    <tr>
      <th>Award</th>
      <th>Date</th>
    </tr>
  </thead>
  <tbody>
    @forelse($awards as $troop)
    <tr>
      <td>
        {{ $troop->award->name }}
      </td>
      <td>
        <x-date-format :value="$troop->created_at"
                       :format="'M j, Y'" />
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="2">
        No Awards ... Yet!
      </td>
    </tr>
    @endforelse
  </tbody>
</x-table>