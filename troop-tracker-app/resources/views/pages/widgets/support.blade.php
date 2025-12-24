<h5 class="mb-3">
  🎯 November Support Goal -
  <span class="text-muted">
    ${{ number_format($goal, 2) }}
  </span>
</h5>

<div class="progress mb-2"
     style="height: 1.5rem;">
  <div class="progress-bar bg-success"
       role="progressbar"
       style="width: {{$progress}}%;"
       aria-valuenow="{{$progress}}"
       aria-valuemin="0"
       aria-valuemax="100">
    {{$progress}}%
  </div>
</div>

<p class="mb-2 text-muted">
  {!! $message !!}
</p>

<a href="{{ config('tracker.donate.url') }}"
   target="_blank"
   class="btn btn-outline-success mb-2">
  ❤️ Support the Garrison
  <span class="fa fa-fw fa-external-link"></span>
</a>
<br>