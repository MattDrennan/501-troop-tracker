@props(['property', 'url', 'placeholder'=>'Search ...'])

<x-input-hidden :property="$property" />

<x-input-text :property="$property"
              class="typeahead"
              placeholder="{{ $placeholder }}"
              autocomplete="off"
              data-search-url="{{ $url }}" />