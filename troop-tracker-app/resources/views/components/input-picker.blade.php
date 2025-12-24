@props(['property', 'value', 'route', 'params'=>[], 'text'=>''])

<div id="picker-container-{{ $property }}"
     class="input-group pointer"
     hx-get="{{ route($route, array_merge($params,['property'=>$property])) }}"
     hx-target="#modal-picker .modal-body"
     hx-trigger="click"
     data-bs-toggle="modal"
     data-bs-target="#modal-picker">

  <x-input-hidden :property="$property"
                  :value="$value" />

  <input type="text"
         readonly
         name="picker-{{ $property }}"
         class="form-control rounded-start pointer"
         value="{{ $text }}" />

  <span class="input-group-text">
    <i class="fa fa-fw fa-search"></i>
  </span>
</div>