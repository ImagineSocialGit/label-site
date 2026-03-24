<div x-show="activeLabel == null || activeLabel == {{ $artist->label->id }}" x-transition class="group hidden lg:block relative w-72 h-72 xl:w-88 xl:h-88 aspect-square overflow-hidden">
    <div x-show="currentArtist.id == {{ $artist->id }}" x-transition.opacity.duration.500ms class="absolute h-full w-full bg-black/55 z-10"></div>
    <div x-show="currentArtist.id !== {{ $artist->id }}" x-transition.opacity.duration.500ms :class="currentArtist.id !== {{ $artist->id }} ? 'group-hover:h-full' : ''" class="absolute flex items-center justify-center bottom-0 h-12 w-full bg-linear-to-t from-alt-black from-10% via-[#000000a6] via-80% to-transparent hover:bg-alt-black/75 duration-200 z-10">
        <span class="absolute top-0 w-fit text-lg font-serif text-secondary pt-2">{{ $artist->name }}</span>
        <div class="flex flex-col space-y-4 items-center">
            <a class="opacity-0 group-hover:opacity-100 block border border-alt-white rounded px-2 py-1 text-xs text-alt-white hover:bg-secondary hover:text-alt-black duration-300" href="/{{ $artist->slug }}">Artist Page</a>
            <a class="opacity-0 group-hover:opacity-100 block border border-alt-white rounded w-fit px-2 py-1 text-xs text-alt-white hover:bg-secondary hover:text-alt-black duration-300" href="{{ $artist->url }}">Visit Website</a>
        </div>
    </div>
    <img :class="currentArtist.id == {{ $artist->id }} ? 'grayscale' : ''"
        class="object-cover {{ $style['desktop']['position'] ?? '' }} w-72 h-72 xl:w-88 xl:h-88 duration-300 lazyload blur-up"
        style="{{ $style['mobile']['styleAttribute'] ?? '' }}"
        src="{{ $style['desktop']['image'] }}/medium{{ $style['desktop']['extension'] }}"
        data-src="{{ $style['desktop']['image'] }}/default{{  $style['desktop']['extension']}}"
        aria-hidden>
</div>

<div x-show="activeLabel == null || activeLabel == {{ $artist->label->id }}" x-transition x-data="{showOptions: false}" @click="showOptions = !showOptions" @click.away="showOptions = false" class="group lg:hidden relative w-48 h-48 sm:w-64 sm:h-64 aspect-square overflow-hidden">
    <div x-show="currentArtist.id == {{ $artist->id }}" x-transition.opacity.duration.500ms class="absolute h-full w-full bg-black/55 z-10"></div>
    <div x-show="currentArtist.id !== {{ $artist->id }}" x-transition.opacity.duration.500ms :class="showOptions ? 'h-full' : 'h-12'" class="absolute flex items-center justify-center bottom-0 w-full px-12 bg-linear-to-t from-alt-black from-10% via-[#000000a6] via-80% to-transparent hover:bg-alt-black/75 duration-200 z-10">
        <span class="absolute top-0 w-fit text-lg font-serif text-secondary pt-2">{{ $artist->name }}</span>
        <div x-show="showOptions" class="flex flex-col space-y-4 items-center">
            <a class="block border border-alt-white rounded px-2 py-1 text-xs text-alt-white hover:bg-secondary hover:text-alt-black duration-300" href="/{{ $artist->slug }}">Artist Page</a>
            <a class="block border border-alt-white rounded w-fit px-2 py-1 text-xs text-alt-white hover:bg-secondary hover:text-alt-black duration-300" href="{{ $artist->url }}">Visit Website</a>
        </div>
    </div>
    <img :class="currentArtist.id == {{ $artist->id }} ? 'grayscale' : ''"
        class="object-cover {{ $style['mobile']['position'] ?? '' }} w-48 h-48 sm:w-64 sm:h-64 duration-300 lazyload blur-up"
        style="{{ $style['mobile']['styleAttribute'] ?? '' }}"
        src="{{ $style['mobile']['image'] }}/medium{{ $style['mobile']['extension'] }}"
        data-src="{{ $style['mobile']['image'] }}/default{{  $style['mobile']['extension']}}"
        aria-hidden>
</div>