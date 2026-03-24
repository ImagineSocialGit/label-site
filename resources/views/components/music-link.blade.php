<a href="{{ $link }}"
    class="ml-8 mb-4 block relative top-0 bottom-0 max-w-64 group font-button"
    >
    <div class="px-2 py-1 text-xl bg-alt-white hover:bg-alt-black border border-alt-white rounded duration-300">
        <span class="flex w-full h-full items-center justify-center opacity-100 group-hover:opacity-0 duration-300 whitespace-nowrap">{{ $slot }}</span>
        <span class="absolute top-0 left-0 flex items-center justify-center h-full w-full text-alt-white opacity-0 group-hover:opacity-100 duration-300 whitespace-nowrap">{{ isset($hoverText) ? $hoverText : $slot }}</span>
    </div>
</a>