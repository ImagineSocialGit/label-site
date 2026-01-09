<x-layout title="Welcome" :universalData="$universalData">
    <div x-data="artistGrid({{ count($artists) }}, {{ json_encode($artists) }})" x-init="pageLoad()" class="relative min-h-215 py-24">

        <img class="absolute w-full object-cover drop-shadow-xl mt-150" src="/images/theme/scratched_silver_square_bar.png" alt="">

        <a :href="'/' + currentArtist.slug" class="block relative px-12 lg:px-0 lg:max-w-5xl xl:max-w-6xl shadow-xl w-full overflow-hidden mx-auto lg:h-180">
            <img class="w-full h-full object-[50%_10%] object-cover" src="{{ config('filesystems.disks.spaces.url') }} + currentArtist.desktop_image" alt="">
            <div class="absolute flex items-center justify-center bottom-0 h-24 w-full px-12 lg:px-0 bg-linear-to-t from-black from-10% via-[#000000a6] via-80% to-transparent z-10">
                <span class="w-fit text-5xl font-serif text-secondary" x-text="currentArtist.name"></span>
            </div>
        </a>
    
        <div class="">
            <div class="relative h-fit flex flex-wrap justify-center gap-2 lg:gap-12 max-w-6xl mx-auto pt-12 z-20">
                @foreach ($artists as $artist)
                <div class="relative w-48 h-48 lg:w-72 lg:h-72 xl:w-88 xl:h-88 aspect-square overflow-hidden">
                    <div x-show="index == {{ $loop->iteration }}" x-transition.opacity.duration.500ms class="absolute h-full w-full bg-black/55"></div>
                    <div x-show="index !== {{ $loop->iteration }}" x-transition.opacity.duration.500ms class="absolute flex items-center justify-center bottom-0 h-12 w-full px-12 lg:px-0 bg-linear-to-t from-alt-black from-10% via-[#000000a6] via-80% to-transparent z-10">
                        <span class="w-fit text-lg font-serif text-secondary pt-2">{{ $artist->name }}</span>
                    </div>
                    <img class="object-top object-cover w-48 h-48 lg:w-72 lg:h-72 xl:w-88 xl:h-88" src="{{ config('filesystems.disks.spaces.url') . $artist->desktop_image }}" aria-hidden>
                </div>
                @endforeach
                
                {{-- <img class="max-w-88 h-full aspect-square overflow-hidden object-right object-cover" src="/images/theme/2_lane_summer.jpg" alt="" srcset="">
                <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/matt_cooper.jpg" alt="" srcset="">
                <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/annie_bosko.jpg" alt="" srcset="">
                <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/spencer_hatcher.jpg" alt="" srcset="">
                <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/runaway_june.jpg" alt="" srcset="">
                <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/ben_gallaher.jpg" alt="" srcset="">
                <img class="max-w-88 h-full aspect-square overflow-hidden object-top object-cover" src="/images/theme/lakelin_lemmings.jpg" alt="" srcset=""> --}}
            </div>
            <div class="relative mt-24">
                <img class="h-full w-full object-cover drop-shadow-xl " src="/images/theme/silver_straight_gradient_small_angle.svg" alt="">
                <h2 class="absolute bottom-0 left-0 right-0 max-w-5xl mx-auto pb-12 font-title font-semibold text-alt-black text-6xl">ABOUT</h2>
            </div>
            <div class="max-w-5xl mx-auto flex flex-col space-y-8 pt-12">
                <p class="text-2xl text-secondary drop-shadow-sm">The Quartz Hill Music Group footprint includes BSB Management as well as Quartz Hill Records and Stone Country Records, both full-service country music labels.</p>
                <p class="text-2xl text-secondary drop-shadow-sm">Quartz Hill Records, founded at the height of the pandemic in 2020, boasts an active roster comprised of chart-topping, multi-Platinum neo-traditionalist Joe Nichols, ACM-nominated and Gold-selling trio Runaway June, soulful country pop duo 2 Lane Summer, and viral, genre-blending singer-songwriter Matt Cooper.</p>
                <p class="text-2xl text-secondary drop-shadow-sm">Stone Country Records, founded in 2021, possesses an active roster that includes celebrated country artist Annie Bosko, triple-threat singer, songwriter and guitarist Ben Gallaher, country traditionalist Spencer Hatcher and rising, girl-next-door Lakelin Lemmings.</p>
                <p class="text-2xl text-secondary drop-shadow-sm">The BSB Management artist roster is home to all the above.</p>
            </div>
        </div>
    </div>
    <script>
        function artistGrid(artistCount, artists){
            return {
                index: 1,
                artistCount: artistCount,
                artists: artists,
                currentArtist: null,
                timer: null,
                pageLoad() {
                    if (this.artistCount > 1){
                        this.autoswap();
                    }
                    this.currentArtist = this.artists[this.index-1];
                    console.log(this.currentArtist);
                    console.log('loaded');
                },
                autoswap(){
                    this.timer = setTimeout(() => {
                        this.index++;
                        if (this.index > this.artistCount){
                            this.index = 1;
                        }
                        this.currentArtist = this.artists[this.index-1];
                        this.autoswap();
                        console.log(this.index);
                    }, 4000);                    
                }
                
            }
        }

        function carousel(mediaCount){
            return {
                index: mediaCount,
                itemCount: mediaCount * 3,
                mediaCount: mediaCount,
                container: null,
                allowScroll: true,
                timer: null,
                pageLoad(element) {
                    this.container = element;
                    if (this.mediaCount > 1){
                    this.hideJump();
                    this.autoScroll();
                    }
                },
                autoScroll() {
                    this.timer = setTimeout(() => {
                        this.index++;
                        this.scrollContainer('right');
                        this.autoScroll();
                    }, 4000);
                },
                attemptScroll(direction){
                    console.log(this.index);
                    if (this.allowScroll){
                        clearTimeout(this.timer);
                        this.scrollContainer(direction);
                        this.allowScroll = false;
                        setTimeout(() =>{
                            this.allowScroll = true;
                            this.autoScroll();
                        }, 500);
                    }
                },
                buttonScroll(newIndex){
                    clearTimeout(this.timer);
                    const containerViewWidth = this.container.clientWidth;
                    const containerScrollWidth = this.container.scrollWidth;
                    const itemSize = containerScrollWidth / this.itemCount;
                    this.index = newIndex;
                    let scrollAmount = (itemSize * this.index) + (itemSize / 2) - (containerViewWidth / 2) ;
                    this.container.scrollTo({
                        top: 0,
                        left: scrollAmount,
                        behavior: "smooth",
                    });
                    this.allowScroll = false;
                    this.autoScroll();
                    setTimeout(() =>{
                        this.allowScroll = true;
                    }, 500);
                },
                scrollContainer(direction){
                    const containerViewWidth = this.container.clientWidth;
                    const containerScrollWidth = this.container.scrollWidth;
                    const itemSize = containerScrollWidth / this.itemCount;
                    if (direction === "right" && this.index === this.mediaCount * 2){
                        this.index = this.mediaCount - 1;
                        this.hideJump();
                        this.index = this.mediaCount;
                    }
                    if (direction === "left" && this.index === this.mediaCount - 1){
                        this.index = this.mediaCount * 2;
                        this.hideJump();
                        this.index = this.mediaCount * 2 - 1;
                    }
                    let scrollAmount = (itemSize * this.index) + (itemSize / 2) - (containerViewWidth / 2) ;
                    this.container.scrollTo({
                        top: 0,
                        left: scrollAmount,
                        behavior: "smooth",
                    });
                },
                hideJump(){
                    const containerViewWidth = this.container.clientWidth;
                    const containerScrollWidth = this.container.scrollWidth;
                    const itemSize = containerScrollWidth / this.itemCount;
                    let scrollAmount = (itemSize * this.index) + (itemSize / 2) - (containerViewWidth / 2) ;
                    this.container.scrollTo({
                        top: 0,
                        left: scrollAmount,
                        behavior: "instant",
                    });
                },
            }
        }
    </script>
</x-layout>