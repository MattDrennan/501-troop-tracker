@props(['model'])

@isset($model)

<div class="row">
  <div class="col-12 text-end">

    @if (isset($model->deleted_at))
    <span class="text-muted">
      soft deleted
      @isset($model->deleted_id)
      by {{ $model->deleted_by->name }}
      @endisset
      {{ $model->deleted_at->diffForHumans() }}
    </span>
    @elseif ($model->created_at == $model->updated_at)
    <span class="text-muted">
      created
      @isset($model->created_id)
      by {{ $model->created_by->name }}
      @endisset
      {{ $model->created_at->diffForHumans() }}
    </span>
    @else
    <span class="text-muted">
      updated
      @isset($model->updated_id)
      by {{ $model->updated_by->name }}
      @endisset
      {{ $model->updated_at->diffForHumans() }}
    </span>
    @endif
  </div>
</div>
@endisset