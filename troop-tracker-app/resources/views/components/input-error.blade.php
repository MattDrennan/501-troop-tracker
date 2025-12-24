@props(['property'])

@error($property)
<p class="form-text text-danger ps-2">{{ $message }}</p>
@enderror