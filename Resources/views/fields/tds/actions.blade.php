<a href="/admin/products/#/fields/{{ $productField->id }}/edit" class="btn btn-xs btn-success">
    <i class="fa fa-edit"></i> Edit
</a>

<button type="button" class="btn btn-danger btn-xs vue-proxy" data-method="deleteField" data-params="{{ json_encode([$productField->id]) }}">
    <i class="fa fa-trash"></i> Delete
</button>