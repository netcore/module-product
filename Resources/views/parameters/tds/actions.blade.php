<a href="/admin/products/#/parameters/{{ $parameter->id }}/edit" class="btn btn-xs btn-success">
    <i class="fa fa-edit"></i> Edit
</a>

<button type="button" class="btn btn-danger btn-xs vue-proxy" data-method="deleteParameter" data-params="{{ json_encode([$parameter->id]) }}">
    <i class="fa fa-trash"></i> Delete
</button>