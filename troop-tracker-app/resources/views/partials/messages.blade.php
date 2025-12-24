@php
$icons = [
'success'=>'fa-circle-check',
'info'=>'fa-circle-info',
'warning'=>'fa-circle-exclamation',
'danger'=>'fa-circle-xmark',
];
@endphp

<div id="flash-messages">
  @if ($flash_messages = $flash->getMessages())
  @foreach ($flash_messages as $type=>$messages)
  @foreach ($messages as $message)
  <div class="alert alert-{{ $type }} alert-dismissible fade show mt-2">
    <strong>
      <i class="fa fa-fw fa-solid {{ $icons[$type] }}"></i>
      {{ $message }}
    </strong>
    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"></button>
  </div>
  @endforeach
  @endforeach
  @endif

  @if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show mt-2">
    <strong>
      Please check the form below for errors
    </strong>
    <ul class="p-0 m-0"
        style="list-style: none;">
      @foreach($errors->all() as $error)
      <li>{{$error}}</li>
      @endforeach
    </ul>
    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"></button>
  </div>
  @endif
</div>