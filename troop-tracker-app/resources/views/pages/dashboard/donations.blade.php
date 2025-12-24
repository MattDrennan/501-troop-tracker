<x-table>
  <thead>
    <tr>
      <th>Date</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
    @forelse($donations as $troop)
    <tr>
      <td>
        <x-date-format :value="$troop->created_at"
                       :format="'M j, Y'" />
      </td>
      <td>
        $ <x-number-format :value="$troop->amount"
                         :decimals="2" />
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="3">
        No Donations ... Yet!
      </td>
    </tr>
    @endforelse
  </tbody>
</x-table>