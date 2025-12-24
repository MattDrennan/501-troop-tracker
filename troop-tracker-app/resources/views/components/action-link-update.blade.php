@props(['url', 'label'=>'Update'])

<x-action-link :label="$label"
               :url="$url"
               :icon="'fa-pencil'" />