<a href="/admin/products/#/products/{{ $product->id }}/edit" class="btn btn-xs btn-success">
    <i class="fa fa-edit"></i> Edit
</a>

<button type="button" class="btn btn-danger btn-xs vue-proxy" data-method="deleteProduct" data-params="{{ json_encode([$product->id]) }}">
    <i class="fa fa-trash"></i> Delete
</button>