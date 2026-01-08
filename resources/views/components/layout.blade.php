<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-alt-black">
    <x-head :title="$title" :metaData="$metaData" :favicon="$favicon" />
    <img class="absolute top-0 bottom-0 left-0 right-0 fade-bottom" src="/images/theme/qhmg_dark_bg.png" alt="">
    <div class="">
    </div>
    <div class="relative z-50">
        <x-hamburger />
    </div>
    @php
        $heroMobileHeight = 'min-h-[500px]';
        $heroHeight = 'h-[780px]';
    @endphp
    <header x-data="{showBackground: false, showLogo: false, showMenus: false}"
        x-init="setTimeout(() => showBackground = true, 100);
        setTimeout(() => showMenus = true, 500);
        setTimeout(() => showLogo = true, 800)"
        class="relative w-full {{ $heroMobileHeight }} {{ $heroHeight }} flex flex-col z-10">
        
        <div class="flex justify-center items-end space-x-16 pt-8">
            <div x-show="showBackground" x-cloak x-transition.duration.1500ms.origin.top class="w-64 h-36 z-0">
                <img class="mx-auto w-64" src="/images/theme/quartzhilllogo_white.png" alt="">
            </div>
            <div x-show="showBackground" x-cloak x-transition.duration.1500ms.origin.top class="z-0">
                <img class="max-w-xl mx-auto" src="/images/theme/qhmg_logo_white.svg" alt="">
            </div>
            <div x-show="showBackground" x-cloak x-transition.duration.1500ms.origin.top class="w-64 h-36 z-0">
                <img class="mx-auto w-40" src="/images/theme/stonecountrylogo_white.png" alt="">
            </div>
        </div>
        
        <div x-show="showLogo" x-cloak x-transition.duration.1500ms.origin.top class="absolute left-0 right-0 max-w-6xl shadow-xl w-full overflow-hidden mx-auto mt-84 lg:h-180">
            <img class="w-full h-full object-[50%_10%] object-cover" src="/images/theme/joe_nichols.jpg" alt="">
            <div class="absolute flex items-center justify-center bottom-0 h-24 w-full bg-linear-to-t from-black from-10% via-[#000000a6] via-80% to-transparent z-10">
                <span class="w-fit text-5xl font-serif text-secondary">Joe Nichols</span>
            </div>
            {{-- <img class="w-full h-full object-[50%_50%] object-cover" src="/images/theme/matt_cooper.jpg" alt=""> --}}
            {{-- <img class="scale-110 object-[50%_50%] object-cover" src="/images/theme/2_lane_summer.jpg" alt=""> --}}
        </div>

    </header>
    <main x-data="{showVideo: false}">
        {{ $slot }}
    </main>
</html>