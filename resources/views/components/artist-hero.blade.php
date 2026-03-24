@props([
    'artists',
    'artistStyles',
    'device',
])
<a :href="'/' + currentArtist.slug"
    {{ $attributes->class(['group relative shadow-xl w-full overflow-hidden mx-auto']) }}
    :aria-label="'View page for ' + currentArtist.name "
    >
    @foreach ($artists as $artist)
        <img x-show="currentArtist.id == {{ $artist->id }}"
            x-transition.opacity.duration.500ms 
            class="absolute w-full h-full object-cover {{ $artistStyles[$artist->name][$device]['position'] ?? '' }} lazyload blur-up"
            style="{{$artistStyles[$artist->name][$device]['styleAttribute'] ?? '' }}"
            src="{{ $artistStyles[$artist->name][$device]['image'] }}/medium{{ $artistStyles[$artist->name][$device]['extension'] }}"
            data-src="{{ $artistStyles[$artist->name][$device]['image'] }}/default{{  $artistStyles[$artist->name][$device]['extension']}}"
            alt="{{ $artist->name }} background image"
            >
        <div x-show="currentArtist.id == {{ $artist->id }}" x-transition.opacity.duration.500ms class="absolute flex flex-col items-center justify-center {{ $device == 'desktop' ? '-bottom-12 group-hover:bottom-0 h-32 bg-linear-to-t from-black from-70% via-[#000000a6] via-90% to-transparent' : 'bottom-0 h-20 px-6 text-center bg-black/90' }} w-full overflow-hidden duration-300 z-10">
            <span class="w-fit {{ $device == 'desktop' ? 'absolute top-5 text-5xl' : 'text-2xl' }} font-serif text-secondary">{{$artist->name}}</span>
            <span class="w-fit {{ $device == 'desktop' ? 'absolute bottom-2 text-xl' : 'text-sm' }} font-serif text-secondary">{{$artist->snippedAbout()}}...</span>
        </div>
    @endforeach
</a>