@props(['property', 'disabled'=>false, 'value'=>''])

<x-input-text :property="$property"
              :value="$value"
              :disabled="$disabled"
              class="date-picker" />