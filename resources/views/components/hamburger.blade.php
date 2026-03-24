<div x-data="{showMenu: false}" class="fixed top-4 left-4 flex flex-col space-y-1">
    <div @click="showMenu = !showMenu" :class="showMenu ? 'w-48' : 'w-10' " class="absolute space-y-1.5 pt-1 px-1 z-20 cursor-pointer transition-all duration-300">
        <div :class="showMenu ? 'w-full bg-alt-black' : 'w-8 bg-secondary'" class="h-0.75 rounded transition-all duration-300"></div>
        <div :class="showMenu ? 'w-full bg-alt-black' : 'w-8 bg-secondary'" class="h-0.75 rounded transition-all duration-300"></div>
        <div :class="showMenu ? 'w-full bg-alt-black' : 'w-8 bg-secondary'" class="h-0.75 rounded transition-all duration-300"></div>
    </div>
    <div x-show="showMenu"
        x-transition:enter.opacity.0.delay.300ms.duration.400ms
        x-transition:leave.opacity.0.duration.400ms
        class="w-48 pl-8 pb-36 flex flex-col space-y-3 bg-linear-to-b from-secondary from-55% to-transparent">
        <a href="/" class="w-fit mt-8 text-2xl font-bold font-press text-alt-black hover:opacity-60 duration-300">Home</a>
        {{-- <a href="/news" class="w-fit text-2xl font-bold text-alt-black hover:opacity-60 duration-300">Artists</a> --}}
        <a href="/#about" @click="showMenu = false" class="w-fit text-2xl font-bold font-press text-alt-black hover:opacity-60 duration-300">About</a>
    </div>
</div>