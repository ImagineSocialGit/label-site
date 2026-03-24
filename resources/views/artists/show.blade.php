<x-layout title="{{ config('app.site-name') }} | {{ $artist->name }}" :universalData="$universalData">

    <div class="lg:max-w-5xl w-full pt-56 lg:pt-72 pb-12 lg:pb-24 flex flex-col space-y-4 lg:space-y-8">
        @php
            $devices = [
                'desktop' => 'hidden lg:block',
                'mobile' => 'lg:hidden px-12'
            ];
        @endphp
        @foreach ($devices as $device => $classes)
        <div class="relative {{ $classes }} shadow-xl max-h-[65vh] w-full overflow-hidden mx-auto">
            <x-artist-image device="{{ $device }}" :styles="$styles" />                
        </div>  
        @endforeach
        <div class="px-8 lg:px-0 mb-8 flex">
            <span class="bg-alt-black w-full h-4 lg:h-6"></span>
            <span class="bg-[#484f57] w-full"></span>
            <span class="bg-[#7f909a] w-full"></span>
            <span class="bg-[#a3aeb3] w-full"></span>
            <span class="bg-[#d2d7d8] w-full"></span>
            <span class="bg-alt-white w-full"></span>
        </div>
        
        @php
            $releaseLinks = [
                'joe-nichols' => 'https://sl.cmdshft.com/gahtlt',
                'matt-cooper' => 'https://mattcooper.ffm.to/home',
                'spencer-hatcher' => 'https://sl.cmdshft.com/honkytonkhideaway',
                '2-lane-summer' => 'https://sl.cmdshft.com/nogoingback',
                'annie-bosko' => 'https://sl.cmdshft.com/californiacowgirlalbum',
                'dusty-black' => 'https://sl.cmdshft.com/IDWBR',
            ];

            if ($featuredRelease){
                $art = $featuredRelease->artwork;
                $link = $featuredRelease->link;
            }
            
        @endphp

        <div class="flex justify-center space-x-12 lg:space-x-0 px-4 lg:px-0 pb-6 lg:pb-0">
            <div class="lg:mx-auto lg:max-w-3xl lg:w-full flex flex-col space-y-8 lg:flex-row justify-center lg:justify-between items-center">
                <div class="text-3xl lg:text-6xl text-alt-white font-title">{{$artist->name}}</div>
                @if ($artist->label->name == 'Quartz Hill Records')
                <img class="w-36 lg:w-44" src="/images/theme/quartzhilllogo_white.png" alt="">
                @endif
                @if ($artist->label->name == 'Stone Country Records')
                <img class="w-24 lg:w-32" src="/images/theme/stonecountrylogo_white.png" alt="">
                @endif
            </div>
            @if ($featuredRelease)
            <div class="lg:hidden max-w-52 flex flex-col items-center space-y-4">
                <img class="" src="{{ $art }}" alt="">
                @if($featuredRelease->isPresave() && !$featuredRelease->isReleased())
                <x-music-link :link="$featuredRelease->link">Presave Now</x-music-link>
                @else
                <x-music-link :link="$featuredRelease->link" hoverText="Listen">Available Now</x-music-link>
                @endif
            </div>
            @endif
        </div>
        <div class="lg:max-w-5xl px-6 lg:px-12 lg:pt-4">
            @if ($featuredRelease)
            <div class="hidden lg:block float-right">
                <img class="ml-8 mb-4 max-w-64" src="{{ $art }}" alt="">
                @if($featuredRelease->isPresave() && !$featuredRelease->isReleased())
                <x-music-link :link="$featuredRelease->link">Presave Now</x-music-link>
                @else
                <x-music-link :link="$featuredRelease->link" hoverText="Listen">Available Now</x-music-link>
                @endif
            </div>
            @endif
            <div class="text-alt-white lg:text-xl">{{$artist->about}}</div>
        </div>
        @if (count($artist->posts) > 0)            
        @php
            $postCount = count($artist->posts);
            $pageCount = intdiv($postCount, 5);
            if (($postCount % 5) > 0){
                $pageCount++;
            }
        @endphp
        <div
            x-data="newsPagination({{ $pageCount }})"
            x-cloak
            x-init="init()"
            class="px-12 lg:px-0 lg:max-w-5xl flex flex-col"
        >

            <h2 class="text-alt-white text-2xl lg:text-5xl font-title pb-2">
                Recent News
            </h2>

            <div
                x-ref="container"
                :style="`min-height:${minHeight}px`"
                class="relative grid"
            >
                
                @foreach ($artist->posts->sortByDesc('publish_date')->values()->chunk(5) as $pageIndex => $posts)

                    <div
                        x-ref="page"
                        class="col-start-1 row-start-1 flex flex-col divide-y divide-alt-white transition-opacity duration-300"
                        :class="page === {{ $pageIndex + 1 }} 
                            ? 'opacity-100 pointer-events-auto relative' 
                            : 'opacity-0 pointer-events-none inset-0'"
                        >

                        @foreach ($posts as $post)
                            @php
                                $post->title = preg_replace('/\<link=".*?\">/', '', $post->title);
                                $post->title = str_replace('</link>', '', $post->title);
                            @endphp

                            <a
                                x-ref="item"
                                :style="`min-height:${minItemHeight}px`"
                                class="group py-1 px-4 text-alt-white font-press text-sm lg:text-lg flex space-x-12 items-center justify-between {{ $loop->last ? 'border-b border-alt-white' : '' }}"
                                href="/{{ $artist->slug }}/{{ $post->slug }}"
                            >
                                <span class="group-hover:opacity-60 duration-300">
                                    {{ $post->title }}
                                </span>

                                <img
                                    class="w-6 group-hover:-translate-x-4 duration-300"
                                    src="/images/theme/double_arrow.svg"
                                    alt=""
                                >
                            </a>

                        @endforeach

                    </div>

                @endforeach

            </div>


            <!-- Pagination -->
            <div class="flex justify-between items-center pt-6 text-alt-white">

                <button
                    @click="prev()"
                    :disabled="page === 1"
                    class="hover:opacity-80 disabled:opacity-30 enabled:cursor-pointer duration-200"
                >
                    Previous
                </button>

                <div class="flex space-x-2">
                    <template x-for="p in pageCount">
                        <button
                            @click="page = p"
                            :class="p === page ? 'underline opacity-50' : 'cursor-pointer hover:opacity-70'"
                            class="px-2 duration-200"
                            x-text="p"
                        ></button>
                    </template>
                </div>

                <button
                    @click="next()"
                    :disabled="page === pageCount"
                    class="hover:opacity-80 disabled:opacity-30 enabled:cursor-pointer duration-200"
                >
                    Next
                </button>

            </div>

        </div>
        <script>
        function newsPagination(pageCount) {
            return {
                page: 1,
                pageCount,
                minHeight: 0,
                minItemHeight: 0,

                init() {
                    this.$nextTick(() => {
                        let tallest = 0;
                        let tallestItem = 0;

                        this.$refs.container.querySelectorAll('[x-ref="page"]').forEach(page => {
                            page.style.display = 'block';
                            tallest = Math.max(tallest, page.offsetHeight);
                            page.style.display = '';
                        })

                        this.$refs.container.querySelectorAll('[x-ref="item"]').forEach(item => {
                            item.style.display = 'block';
                            tallestItem = Math.max(tallestItem, item.offsetHeight);
                            item.style.display = '';
                        })

                        this.minHeight = tallest;
                        this.minItemHeight = tallestItem;
                    })
                },

                next() {
                    if (this.page < this.pageCount) this.page++
                },

                prev() {
                    if (this.page > 1) this.page--
                }
            }
        }
        </script>
        @endif
    </div>
</x-layout>