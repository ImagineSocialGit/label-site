<a {{ $attributes->merge(['class' => 'w-fit px-2 py-1 bg-alt-black border-2 border-alt-black rounded-lg text-white text-lg
        hover:bg-white hover:text-alt-black duration-300 cursor-pointer']) }}
    >
    {{$slot}}
</a>