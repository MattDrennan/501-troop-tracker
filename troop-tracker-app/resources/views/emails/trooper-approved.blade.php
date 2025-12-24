@extends('layouts.email')

@section('message')
<p>
  Trooper!
</p>
<p>
  Congratulations. Your application has survived the scrutiny of the
  Imperial bureaucracy and your armor has been deemed
  <strong>adequately intimidating</strong>. You are now officially
  filed with the <em>{{ config('tracker.name') }} Troop Tracker</em>.
</p>
<p>
  From this moment forward, you'll be expected to:
</p>
<ul>
  <li>March in formation (or at least look like you're trying).</li>
  <li>Keep a watchful eye on those wandering mercs - they're slippery when you're not looking.</li>
  <li>Pretend you don't notice that the Rebels always seem to escape.</li>
</ul>
<p>
  Please review the upcoming deployments. In the meantime, polish that bucket - we expect
  it to shine brighter than a Tatooine sunrise.
</p>
<p style="margin-top:20px; font-weight:bold; color:#333;">
  - Imperial Administration, {{ config('tracker.name') }}
</p>

@endsection