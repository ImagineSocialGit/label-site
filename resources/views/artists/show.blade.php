<x-layout title="{{ config('app.site-name') }} | {{ $artist->name }}" :universalData="$universalData">

    <div class="pt-56 lg:pt-72 pb-12 lg:pb-24 flex flex-col space-y-4 lg:space-y-8">
        <div class="relative px-12 lg:px-0 shadow-xl max-h-[65vh] lg:max-w-5xl w-full overflow-hidden mx-auto">
            <img class="max-w-5xl w-full h-full object-cover" src="{{ config('filesystems.disks.spaces.url') . $artist->desktop_image }}" alt="">
        </div>
        <div class="px-8 lg:px-0 lg:max-w-5xl mb-8 flex">
            <span class="bg-alt-black w-full h-4 lg:h-6"></span>
            <span class="bg-[#484f57] w-full"></span>
            <span class="bg-[#7f909a] w-full"></span>
            <span class="bg-[#a3aeb3] w-full"></span>
            <span class="bg-[#d2d7d8] w-full"></span>
            <span class="bg-alt-white w-full"></span>
        </div>
        
        @php
            $releases = [
                'joe-nichols' => 'https://bsb-mgmt-storage.nyc3.cdn.digitaloceanspaces.com/joenichols.com/uploads/hGvaq9HntfAgwYoiJKgmgvXGKNHhbUsg8zfZ8P81.jpg',
                'matt-cooper' => 'https://bsb-mgmt-storage.nyc3.cdn.digitaloceanspaces.com/uploads/music/Home-art-442115_21_11_25.jpeg',
                'spencer-hatcher' => 'https://stonecountryrecords.com/images/music/hth_final_cover_min.jpg',
                '2-lane-summer' => 'https://bsb-mgmt-storage.nyc3.cdn.digitaloceanspaces.com/2lanesummer.com/uploads/iV5lUGOhyCySQmIvVmWVPghUErjpcEroDejctZ2q.jpg',
                'annie-bosko' => 'https://cloudinary-cdn.ffm.to/s--UIXlkaf5--/f_jpg/https%3A%2F%2Fimagestore.ffm.to%2Flink%2Fa07a47e29ca35962befc049f2a235985.jpeg',
                'dusty-black' => 'https://is1-ssl.mzstatic.com/image/thumb/Music221/v4/bf/1b/a8/bf1ba82c-eb34-79d5-4e69-e0f2e534f6bd/659459549556.jpg/600x600cc.webp',
            ];
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
            } else {
                $art = $releases[$artist->slug];
                $link = $releaseLinks[$artist->slug];   
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
            <div class="lg:hidden max-w-52 flex flex-col items-center space-y-4">
                <img class="" src="{{ $art }}" alt="">
                <a href="{{ $link }}" class="flex justify-center">
                    <span class="block w-fit px-2 py-1 text-xl text-center bg-alt-white text-alt-black rounded">Available Now</span>
                </a>
            </div>
        </div>

        <div class="lg:max-w-5xl px-6 lg:px-12 lg:pt-4">
            <div class="hidden lg:block float-right">
                <img class="ml-8 mb-4 max-w-64" src="{{ $art }}" alt="">
                <a href="{{ $link }}" class="ml-8 mb-4 flex justify-center">
                    <span class="block w-fit px-2 py-1 text-xl text-center bg-alt-white text-alt-black rounded">Available Now</span>
                </a>
            </div>
            <div class="text-alt-white lg:text-xl">{{$artist->about}}</div>
        </div>
        @php
            $postCount = count($artist->posts);
            $pageCount = 2;
        @endphp
        <div x-data="{page: 1, postCount: {{ $postCount }}, pageCount: {{ $pageCount }}}" class="lg:max-w-5xl flex flex-col divide-y divide-alt-white">
            <h2 class="text-alt-white text-2xl lg:text-5xl font-title pb-2">Recent News</h2>
            @foreach ($artist->posts->sortByDesc('publish_date') as $post)
                @php
                    $post->title = preg_replace('/\<link=".*?\">/', '', $post->title);
                    $post->title = str_replace('</link>', '', $post->title);
                @endphp
                <a x-show="" class="group px-4 py-4 text-alt-white font-press text-lg flex space-x-12 items-center justify-between" href="/{{ $artist->slug }}/{{ $post->slug }}">
                    <span class="group-hover:opacity-60 duration-300">{{ $post->title }}</span>
                    <img class="w-6 group-hover:-translate-x-4 duration-300" src="/images/theme/double_arrow.svg" alt="">
                </a>
            @endforeach
            <div class=""></div>
        </div>
    </div>
</x-layout>