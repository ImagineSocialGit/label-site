<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-alt-black">
    <x-head :title="$title" :metaData="$universalData->metaData" :favicon="$universalData->favicon" />

    <div x-data="{hide: false}" x-show="!hide" x-init="setTimeout(() => hide = true, 200)" x-transition.opacity.duration.400ms class="fixed bg-alt-black top-0 bottom-0 left-0 right-0 z-50"></div>
    
    @if (request()->is('/'))
    <img class="absolute top-0 bottom-0 left-0 right-0 fade-bottom -z-10" src="/images/theme/qhmg_dark_bg.png" alt="">
    @endif

    <div class="relative z-40">
        <x-hamburger />
    </div>
    @php
        $heroMobileHeight = 'min-h-[200px]';
        $heroHeight = 'h-[280px]';
    @endphp
    <header x-data="{showBackground: false, showLogo: false, showMenus: false}"
        x-init="setTimeout(() => showBackground = true, 100);
        setTimeout(() => showMenus = true, 500);
        setTimeout(() => showLogo = true, 700)"
        class="relative w-full {{ $heroMobileHeight }} {{ $heroHeight }} flex flex-col z-10">
        
        <div class="flex justify-center items-end space-x-16 pt-8">
            @if (request()->is('/'))
            <div x-show="showBackground" x-cloak x-transition.delay.800ms.duration.1900ms.origin.top class="w-64 h-36 z-0">
                <img class="mx-auto w-64" src="/images/theme/quartzhilllogo_white.png" alt="">
            </div>
            @endif
            <div x-show="showBackground" x-cloak x-transition.duration.1500ms.origin.top class="z-0">
                @if (request()->is('/'))
                <img class="max-w-xl mx-auto" src="/images/theme/qhmg_logo_white.svg" alt="">
                @else
                <img class="max-w-sm mx-auto" src="/images/theme/qhmg_logo_white.svg" alt="">
                @endif
            </div>
            @if (request()->is('/'))
            <div x-show="showBackground" x-cloak x-transition.delay.800ms.duration.1900ms.origin.top class="w-64 h-36 z-0">
                <img class="mx-auto w-40" src="/images/theme/stonecountrylogo_white.png" alt="">
            </div>
            @endif
        </div>
    </header>
    @if (request()->is('/'))
    <main class="">
        {{ $slot }}
    </main>
    @else
    <main class="absolute top-0 bottom-0 left-0 right-0 flex flex-col items-center justify-center min-h-screen h-fit overflow-hidden">
        <img class="absolute top-0 bottom-0 left-0 right-0 h-full w-screen object-cover object-top -z-10" src="/images/theme/qhmg_dark_bg.png" alt="">
        <div class="grow w-full flex flex-col items-center justify-center">
            {{ $slot }}
        </div>
    </main>
    @endif
    @auth
    <a href="/admin-{{ config('app.initials') }}/artists" class="block fixed bottom-4 left-4 bg-alt-white text-lg px-2 py-1 rounded hover:opacity-60 duration-300">Manage Artists</a>
    @endauth
</html>