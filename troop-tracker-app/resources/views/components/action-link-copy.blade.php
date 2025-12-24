@props(['url', 'label'=>'Copy'])

<x-action-link :label="$label"
               :url="$url"
               :icon="'fa-copy'" />