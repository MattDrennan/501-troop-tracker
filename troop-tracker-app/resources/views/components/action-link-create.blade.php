@props(['url', 'label'=>'Add'])

<x-action-link :label="$label"
               :url="$url"
               :icon="'fa-add text-success'" />