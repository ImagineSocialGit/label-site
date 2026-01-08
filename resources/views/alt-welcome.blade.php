<x-layout title="Title" :metaData="$metaData" :favicon="$favicon">
    {{-- <a href="/welcome-2" class="fixed bottom-4 right-4 shadow-xl bg-white px-2 py-1 font-semibold rounded-lg">View Alt Welcome</a> --}}
    <div class="bg-black min-h-215 pb-12 bg-radial-[at_80%_75%] from-secondary/25 via-alt-black via-25% to-70% to-black">
        <img class="absolute w-full object-cover drop-shadow-xl" src="/images/theme/silver_wave_gradient.svg" alt="">
        <div class="relative h-fit flex flex-wrap justify-center gap-12 max-w-6xl mx-auto pt-84 z-20">
            <div class="relative max-w-88 h-full aspect-square overflow-hidden">
                <div class="absolute h-full w-full bg-black/55"></div>
                <img class="object-top object-cover" src="/images/theme/joe_nichols.jpg" alt="" srcset="">
            </div>
            <img class="max-w-88 h-full aspect-square overflow-hidden object-right object-cover" src="/images/theme/2_lane_summer.jpg" alt="" srcset="">
            <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/matt_cooper.jpg" alt="" srcset="">
            <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/annie_bosko.jpg" alt="" srcset="">
            <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/spencer_hatcher.jpg" alt="" srcset="">
            <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/runaway_june.jpg" alt="" srcset="">
            <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/ben_gallaher.jpg" alt="" srcset="">
            <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/lakelin_lemmings.jpg" alt="" srcset="">
        </div>
        <div class="relative mt-24">
            <img class="h-full w-full object-cover drop-shadow-xl " src="/images/theme/silver_straight_gradient_small_angle_curved.svg" alt="">
            <h2 class="absolute bottom-0 left-0 right-0 max-w-5xl mx-auto pb-12 font-title font-semibold text-alt-black text-6xl">ABOUT</h2>
        </div>
        <div class="max-w-5xl mx-auto flex flex-col space-y-8 pt-12">
            <p class="text-2xl text-secondary drop-shadow-sm">The Quartz Hill Music Group footprint includes BSB Management as well as Quartz Hill Records and Stone Country Records, both full-service country music labels.</p>
            <p class="text-2xl text-secondary drop-shadow-sm">Quartz Hill Records, founded at the height of the pandemic in 2020, boasts an active roster comprised of chart-topping, multi-Platinum neo-traditionalist Joe Nichols, ACM-nominated and Gold-selling trio Runaway June, soulful country pop duo 2 Lane Summer, and viral, genre-blending singer-songwriter Matt Cooper.</p>
            <p class="text-2xl text-secondary drop-shadow-sm">Stone Country Records, founded in 2021, possesses an active roster that includes celebrated country artist Annie Bosko, triple-threat singer, songwriter and guitarist Ben Gallaher, country traditionalist Spencer Hatcher and rising, girl-next-door Lakelin Lemmings.</p>
            <p class="text-2xl text-secondary drop-shadow-sm">The BSB Management artist roster is home to all the above.</p>
        </div>
    </div>
</x-layout>