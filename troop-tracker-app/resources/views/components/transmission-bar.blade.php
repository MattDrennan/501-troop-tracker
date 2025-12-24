@props(['id'=>'transmission-bar-' . uniqid()])

<div id="transmission-bar-{{ $id }}"
     class="container-fluid p-0 m-0 mb-1 htmx-indicator">
  <div class="progress rounded-0">
    <div class="progress-bar progress-bar-striped progress-bar-animated"
         style="width: 100%"></div>
  </div>
</div>