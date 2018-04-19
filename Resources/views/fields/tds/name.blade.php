@if(languages()->count() > 1)
    <ul class="list-unstyled m-a-0">
        @foreach(languages() as $language)
            <li>
                <b class="text-uppercase">{{ $language->iso_code }}:</b> {{ $productField->translate($language->iso_code)->name ?? '' }}
            </li>
        @endforeach
    </ul>
@else
    {{ $productField->name }}
@endif