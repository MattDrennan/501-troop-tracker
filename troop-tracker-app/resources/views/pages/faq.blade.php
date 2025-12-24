@extends('layouts.base')

@section('content')

<x-page-title>
  FAQ
</x-page-title>

@foreach ($videos as $key=>$label)
<h4>{{ $label }}</h4>
<p>
  <iframe width="100%"
          height="315"
          src="https://www.youtube.com/embed/{{ $key }}"
          title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen></iframe>
</p>
@endforeach

<h4>Troop Tracker Manual</h4>
<p>
  <a href="https://github.com/MattDrennan/501-troop-tracker/blob/master/manual/troop_tracker_manual.pdf"
     target="_blank">Click here to view PDF manual.</a>
</p>

<h4>I cannot login / I forgot my password</h4>
<p>
  The Troop Tracker has been integrated with the boards. You must use your <b>{{ config('tracker.forum.name')
    }}</b> boards
  username and passwordto login to Troop Tracker. To recover your password, use password recovery on the
  <b>{{ config('tracker.forum.name') }}</b> forum. If you
  continue to have issues logging into your account, your <b>{{ config('tracker.forum.name') }}</b> forum
  username may not
  match the Troop Tracker
  records. Contact the <b>{{ config('tracker.forum.name') }}</b> Webmaster or post a help thread on the forums
  to get this
  corrected.
</p>

<h4>I am missing troop data / My troop data is incorrect</h4>
<p>
  Please refer to your unit leader to get this corrected.
</p>

<h4>I am now a member of another organization and need access to their costumes.</h4>
<p>
  Please refer to your unit / organization leader to get added to the roster.
</p>

<h4>My costumes are not showing on my profile / I am missing a costume on my profile</h4>
<p>
  The troop tracker automatically scrapes several different organization databases for your costume data. If your costume data
  is not showing, make sure your ID numbers and forum usernames are accurate. If the aforementioned information is
  correct, then refer to your unit / organization leadership, as this data is missing on their end.
</p>

<h4>How do I know I confirmed a troop?</h4>
<p>
  The troop will be listed on your troop tracker profile, or under your stats on the troop tracker page. When you
  confirm a troop, your status will change from "Going" to "Attended".
</p>

<h4>I need a costume added to the troop tracker.</h4>
<p>
  Please notify your unit leader, or e-mail the Garrison Web Master directly. See below for e-mail.
</p>

<h4>I need information on joining the 501st Legion.</h4>
<p>
  <a href="https://databank.501st.com/databank/Join_Us"
     target="_blank">Click here to learn how to join.</a>
</p>

<h4>Contact Garrison Web Master</h4>
<p>
  If you have read and reviewed all the material above and are still experiencing issues, or have noticed a bug on the
  website, please <a href="mailto:{{ config('forum.webmaster') }}">send an e-mail here</a>.
</p>
@endsection