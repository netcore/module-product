@if(!$parameter->categories->count())
    <i>No categories attached.</i>
@else
    @foreach($parameter->categories as $category)
        <span class="label label-default">
            {{ $category->chainedName }}
        </span>
    @endforeach
@endif