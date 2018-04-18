@if($product->images->count())
    @php($image = $product->images->where('is_preview', true)->first() ?? $product->images->first())

    <img src="{{ $image->image->url('thumb') }}" alt="{{ $image->title }}" class="preview-image img-responsive">
@else
    <img src="//placehold.it/150" alt="No image">
@endif
