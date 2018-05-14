@if(languages()->count() > 1)
    <ul class="list-unstyled m-a-0">
        @foreach(languages() as $language)
            <li>
                <b class="text-uppercase">{{ $language->iso_code }}:</b> {{ $product->translate($language->iso_code)->title ?? '' }}
            </li>
        @endforeach
    </ul>
@else
    {{ $product->title }}
@endif