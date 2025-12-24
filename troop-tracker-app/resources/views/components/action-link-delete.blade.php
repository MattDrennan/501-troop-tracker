@props(['url', 'label'=>'Delete'])

<x-action-link :label="$label"
               :url="$url"
               :icon="'fa-times text-danger'" />