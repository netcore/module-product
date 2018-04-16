<ul class="m-b-0">
    @foreach($productField->translations as $translation)
        <li><b>{{ strtoupper($translation->locale) }}:</b> {{ $translation->name }}</li>
    @endforeach
</ul>