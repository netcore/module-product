@if(!$productField->categories->count())
    <i>No categories attached.</i>
@else
    @foreach($productField->categories as $category)
        <span class="label label-default">
            {{ $category->chainedName }}
        </span>
    @endforeach
@endif