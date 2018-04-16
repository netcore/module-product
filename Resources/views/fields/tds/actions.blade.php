<a href="{{ route('product::fields.edit', $productField) }}" class="btn btn-xs btn-primary" data-vue-method="editField">
    <i class="fa fa-edit"></i> Edit
</a>

<a href="javascript:;" class="btn btn-xs btn-danger delete-field" data-route="{{ route('product::fields.destroy', $productField) }}">
    <i class="fa fa-trash"></i> Delete
</a>