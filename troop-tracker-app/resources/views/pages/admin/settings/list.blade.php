@extends('layouts.base')

@section('content')

<x-transmission-bar :id="'settings'" />

<x-page-title>
  Site Settings
</x-page-title>

<x-table>
  <thead>
    <tr>
      <th>
        Setting
      </th>
      <th>
        Value
      </th>
    </tr>
  </thead>
  @forelse($settings as $setting)
  <tr>
    <td>
      {{ $setting->key }}
    </td>
    <td>
      <x-input-text :property="'setting.'. $setting->key"
                    :value="$setting->value"
                    hx-post="{{ route('admin.settings.update-htmx', ['setting'=>$setting]) }}"
                    hx-params="*"
                    hx-trigger="change"
                    hx-indicator="#transmission-bar-settings" />
    </td>
  </tr>
  @empty
  <tr>
    <td colspan="3">
      No Settings Defined
    </td>
  </tr>
  @endforelse
</x-table>
@endsection