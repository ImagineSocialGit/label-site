<img class="max-w-5xl max-h-[65vh] w-full h-full object-cover {{ $styles[$device]['position'] ?? '' }} lazyload blur-up"
    style="{{ $styles[$device]['styleAttribute'] ?? '' }}"
    src="{{ $styles[$device]['image'] }}/medium{{ $styles[$device]['extension'] }}"
    data-src="{{ $styles[$device]['image'] }}/default{{ $styles[$device]['extension'] }}" alt="">