<div class="group relative w-48 h-48 sm:w-64 sm:h-64 lg:w-72 lg:h-72 xl:w-88 xl:h-88 aspect-square overflow-hidden">
    <div x-show="index == {{ $loop->iteration }}" x-transition.opacity.duration.500ms class="absolute h-full w-full bg-black/55 z-10"></div>
    <div x-show="index !== {{ $loop->iteration }}" x-transition.opacity.duration.500ms :class="index !== {{ $loop->iteration }} ? 'group-hover:h-full' : ''" class="absolute flex items-center justify-center bottom-0 h-12 w-full px-12 lg:px-0 bg-linear-to-t from-alt-black from-10% via-[#000000a6] via-80% to-transparent hover:bg-alt-black/75 duration-200 z-10">
        <span class="absolute top-0 w-fit text-lg font-serif text-secondary pt-2">{{ $artist->name }}</span>
        <div class="flex flex-col space-y-4 items-center">
            <a class="opacity-0 group-hover:opacity-100 block border border-alt-white rounded px-2 py-1 text-xs text-alt-white hover:bg-secondary hover:text-alt-black duration-300" href="/{{ $artist->slug }}">Artist Page</a>
            <a class="opacity-0 group-hover:opacity-100 block border border-alt-white rounded w-fit px-2 py-1 text-xs text-alt-white hover:bg-secondary hover:text-alt-black duration-300" href="{{ $artist->url }}">Visit Website</a>
        </div>
    </div>
    <img :class="index == {{ $loop->iteration }} ? 'grayscale' : ''" class="object-top object-cover w-48 h-48 sm:w-64 sm:h-64 lg:w-72 lg:h-72 xl:w-88 xl:h-88 duration-300" src="{{ config('filesystems.disks.spaces.url') . $artist->desktop_image }}" aria-hidden>
</div>