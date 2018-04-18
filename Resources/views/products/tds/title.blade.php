@if(languages()->count() > 1)
    <ul class="list-unstyled">
        @foreach(languages() as $language)
            <li>
                <b class="text-uppercase">{{ $language->iso_code }}:</b> {{ optional($product->translate($language->iso_code))->title }}
            </li>
        @endforeach
    </ul>
@else
    {{ $product->title }}
@endif