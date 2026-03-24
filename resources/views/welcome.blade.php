<x-layout title="Welcome" :universalData="$universalData">
    <div x-data="artistGrid({{ count($artists) }}, {{ json_encode($artists) }})" x-init="pageLoad()" class="relative min-h-215">
        <div class="max-w-6xl mx-auto flex flex-col lg:flex-row justify-center lg:justify-between lg:items-end space-y-6 lg:space-y-0 py-8">
            <div class="hidden lg:block w-66 max-h-40 z-0">
                <div @click="filterByLabel(2)" 
                    :class="activeLabel == 2 ? 'opacity-20' : 'cursor-pointer'" 
                    class="relative group duration-300">
                    
                    <img class="mx-auto w-64 p-2" src="/images/theme/quartzhilllogo_white.png" alt="">

                    <!-- Hover Overlay -->
                    <div 
                        class="absolute inset-0 flex items-center justify-center 
                            bg-black/80 transition-opacity duration-300"
                        :class="{
                            'opacity-100 pointer-events-none': showIntroOverlay && activeLabel === null,
                            'opacity-0 group-hover:opacity-100': !showIntroOverlay && activeLabel !== 2,
                            'opacity-0 pointer-events-none': activeLabel === 2
                        }">
                        
                        <span class="text-white text-center px-12 text-lg font-semibold">
                            View Artists On This Label
                        </span>
                    </div>
                </div>
            </div>

            <div :class="activeLabel !== null ? 'cursor-pointer' : ''" class="relative group lg:mb-20">
                <img
                    class="max-w-xs lg:max-w-xl mx-auto" 
                    src="/images/theme/qhmg_logo_white.svg" alt="">

                <!-- Hover Overlay -->
                <div @click="clearFilter()"
                    class="absolute inset-0 flex items-center justify-center 
                        bg-black/80 transition-opacity duration-300"
                    :class="activeLabel !== null 
                        ? 'opacity-0 group-hover:opacity-100' 
                        : 'opacity-0 pointer-events-none'">
                    
                    <span class="text-white text-center px-12 text-xl font-semibold">
                        Clear Selected Label
                    </span>
                </div>
            </div>

            <div class="hidden lg:block w-60 p-2 max-h-40 z-0">
                <div @click="filterByLabel(3)" 
                    :class="activeLabel == 3 ? 'opacity-20' : 'cursor-pointer'" 
                    class="relative group duration-300">
                    
                    <img class="mx-auto w-40" src="/images/theme/stonecountrylogo_white.png" alt="">

                    <!-- Hover Overlay -->
                    <div 
                        class="absolute inset-0 flex items-center justify-center 
                            bg-black/80 transition-opacity duration-300"
                        :class="{
                            'opacity-100 pointer-events-none': showIntroOverlay && activeLabel === null,
                            'opacity-0 group-hover:opacity-100': !showIntroOverlay && activeLabel !== 3,
                            'opacity-0 pointer-events-none': activeLabel === 3
                        }">
                        
                        <span class="text-white text-center px-12 text-lg font-semibold">
                            View Artists On This Label
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex lg:hidden space-x-12 justify-center items-center">
                <div @click="filterByLabel(2)" class="w-36">
                    <img class="mx-auto" src="/images/theme/quartzhilllogo_white.png" alt="">
                </div>
                <div @click="filterByLabel(3)" class="w-36">
                    <img class="mx-auto w-24" src="/images/theme/stonecountrylogo_white.png" alt="">
                </div>
            </div>
        </div>

        <img class="lg:hidden absolute w-full object-cover drop-shadow-xl mt-72" src="/images/theme/scratched_silver_square_bar.png" alt="">

        <img class="hidden lg:block absolute w-full object-cover drop-shadow-xl lg:mt-150" src="/images/theme/scratched_silver_square_bar.png" alt="">

        <x-artist-hero
            :artists="$artists"
            :artistStyles="$styles"
            device="desktop"
            class="hidden lg:block max-w-5xl xl:max-w-6xl h-180"
            />
        <x-artist-hero
            :artists="$artists"
            :artistStyles="$styles"
            device="mobile"
            class="block lg:hidden px-2 max-w-md h-80"
            />
    
        <div class="">
            <div class="relative h-fit flex flex-wrap justify-center gap-2 lg:gap-12 max-w-6xl mx-auto pt-12 z-20">
                @foreach ($artists as $artist)
                <x-artist-tile :artist="$artist" :style="$styles[$artist->name]" :loop="$loop" />
                @endforeach
            </div>
            <div class="relative mt-24">
                <img class="h-24 lg:h-full lg:w-full object-cover drop-shadow-xl " src="/images/theme/silver_straight_gradient_small_angle.svg" alt="">
                <h2 class="absolute bottom-0 left-0 right-0 lg:max-w-5xl mx-auto pl-12 lg:pl-0 pb-1 lg:pb-8 xl:pb-12 font-title font-semibold text-alt-black text-4xl lg:text-6xl">ABOUT</h2>
            </div>
            <div id="about" class="max-w-5xl mx-auto flex flex-col space-y-8 px-8 lg:px-0 pb-12 pt-12 font-long-text">
                <p class="text-lg lg:text-2xl text-secondary drop-shadow-sm">The Quartz Hill Music Group footprint includes BSB Management as well as Quartz Hill Records and Stone Country Records, both full-service country music labels.
                <p class="text-lg lg:text-2xl text-secondary drop-shadow-sm"><b>Quartz Hill Records</b>, founded at the height of the pandemic in 2020, boasts an active roster comprised of chart-topping, multi-Platinum neo-traditionalist <a href="https://www.joenichols.com/" target="_blank" rel="noopener noreferrer">Joe Nichols</a>, rising, girl-next-door <a href="https://lakelinlemmings.com/" target="_blank" rel="noopener noreferrer">Lakelin Lemmings</a>, soulful country pop duo <a href="https://www.2lanesummer.com/" target="_blank" rel="noopener noreferrer">2 Lane Summer</a> and viral, genre-blending singer-songwriter <a href="https://www.tiktok.com/@realmattcooper?lang=en" target="_blank" rel="noopener noreferrer">Matt Cooper</a>.
                <p class="text-lg lg:text-2xl text-secondary drop-shadow-sm"><b>Stone Country Records</b>, founded in 2021, possesses an active roster that includes celebrated country artist <a href="https://www.instagram.com/anniebosko/" target="_blank" rel="noopener noreferrer">Annie Bosko</a>, triple-threat singer, songwriter and guitarist <a href="https://www.instagram.com/ben_gallaher/" target="_blank" rel="noopener noreferrer">Ben Gallaher</a>, modern country traditionalist <a href="https://www.instagram.com/spencerhatcherofficial/" target="_blank" rel="noopener noreferrer">Spencer Hatcher</a> and soulful country newcomer Dusty Black. 
                <p class="text-lg lg:text-2xl text-secondary drop-shadow-sm">The <b>BSB Management</b> artist roster is home to all the above.</p>
            </div>
        </div>
    </div>
    <script>
        function artistGrid(artistCount, artists){
            return {
                index: 0,
                artistCount: artistCount,
                artists: artists,
                filteredArtists: artists,
                currentArtist: null,
                activeLabel: null,
                timer: null,
                showIntroOverlay: true,

                pageLoad() {
                    this.currentArtist = this.filteredArtists[this.index];

                    setTimeout(() => {
                        this.showIntroOverlay = false;
                    }, 2000);

                    if (this.filteredArtists.length > 1) {
                        this.startAutoswap();
                    }
                },

                filterByLabel(labelId) {
                    // toggle off if already active
                    if (this.activeLabel === labelId) {
                        this.clearFilter();
                        return;
                    }

                    this.activeLabel = labelId;

                    this.filteredArtists = this.artists.filter(a => {
                        return a.label.id === labelId;
                    });

                    this.resetLoop();
                },

                clearFilter() {
                    this.activeLabel = null;
                    this.filteredArtists = this.artists;

                    this.stopAutoswap();

                    // keep index but normalize it to new array
                    this.index = this.index % this.filteredArtists.length;
                    this.currentArtist = this.filteredArtists[this.index];

                    if (this.filteredArtists.length > 1) {
                        this.startAutoswap();
                    }
                },

                resetLoop() {
                    this.stopAutoswap();

                    this.index = 0;
                    this.currentArtist = this.filteredArtists[0];

                    if (this.filteredArtists.length > 1) {
                        this.startAutoswap();
                    }
                },

                startAutoswap() {
                    this.autoswap();
                },

                stopAutoswap() {
                    clearTimeout(this.timer);
                },

                autoswap(){
                    this.timer = setTimeout(() => {
                        this.index = (this.index + 1) % this.filteredArtists.length;
                        this.currentArtist = this.filteredArtists[this.index];
                        this.autoswap();
                    }, 4000);                    
                }
            }
        }
    </script>
</x-layout>