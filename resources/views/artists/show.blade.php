<x-layout title="{{ config('app.site-name') }} | {{ $artist->name }}" :universalData="$universalData">

    <div class="lg:pt-72 lg:pb-24 flex flex-col lg:space-y-8">
        <div class="relative px-12 lg:px-0 shadow-xl max-h-[65vh] lg:max-w-5xl w-full overflow-hidden mx-auto">
            <img class="max-w-5xl w-full h-full object-cover" src="{{ config('filesystems.disks.spaces.url') . $artist->desktop_image }}" alt="">
        </div>
        <div class="max-w-5xl mb-8 flex">
            <span class="bg-alt-black w-full h-6"></span>
            <span class="bg-[#484f57] w-full"></span>
            <span class="bg-[#7f909a] w-full"></span>
            <span class="bg-[#a3aeb3] w-full"></span>
            <span class="bg-[#d2d7d8] w-full"></span>
            <span class="bg-alt-white w-full"></span>
        </div>
        <div class="lg:mx-auto lg:max-w-3xl lg:w-full flex justify-between items-center">
            <div class="lg:text-6xl text-alt-white font-title">{{$artist->name}}</div>
            @if ($artist->label->name == 'Quartz Hill Records')
            <img class="w-44" src="/images/theme/quartzhilllogo_white.png" alt="">           
            @endif
            @if ($artist->label->name == 'Stone Country Records')
            <img class="w-32" src="/images/theme/stonecountrylogo_white.png" alt="">
            @endif
        </div>

        <div class="lg:max-w-5xl lg:px-12 lg:pt-4">
            <div class="float-right">
                @php
                    $releases = [
                        'joe-nichols' => 'https://bsb-mgmt-storage.nyc3.cdn.digitaloceanspaces.com/joenichols.com/uploads/hGvaq9HntfAgwYoiJKgmgvXGKNHhbUsg8zfZ8P81.jpg',
                        'matt-cooper' => 'https://bsb-mgmt-storage.nyc3.cdn.digitaloceanspaces.com/uploads/music/Home-art-442115_21_11_25.jpeg',
                        'ben-gallaher' => 'https://bsb-mgmt-storage.nyc3.cdn.digitaloceanspaces.com/bengallaher.com/uploads/music/Time-art-093009_25_11_25.jpeg',
                        'lakelin-lemmings' => 'https://bsb-mgmt-storage.nyc3.cdn.digitaloceanspaces.com/lakelinlemmings.com/uploads/music/What-Are-We-Doing-art-134708_09_01_26.jpeg',
                        'spencer-hatcher' => '',
                        '2-lane-summer' => 'https://bsb-mgmt-storage.nyc3.cdn.digitaloceanspaces.com/2lanesummer.com/uploads/iV5lUGOhyCySQmIvVmWVPghUErjpcEroDejctZ2q.jpg',
                        'annie-bosko' => 'https://cloudinary-cdn.ffm.to/s--UIXlkaf5--/f_jpg/https%3A%2F%2Fimagestore.ffm.to%2Flink%2Fa07a47e29ca35962befc049f2a235985.jpeg',
                        'dusty-black' => 'https://is1-ssl.mzstatic.com/image/thumb/Music221/v4/bf/1b/a8/bf1ba82c-eb34-79d5-4e69-e0f2e534f6bd/659459549556.jpg/600x600cc.webp',
                    ];
                    $releaseLinks = [
                        'joe-nichols' => 'https://sl.cmdshft.com/gahtlt',
                        'matt-cooper' => 'https://mattcooper.ffm.to/home',
                        'ben-gallaher' => 'https://sl.cmdshft.com/time',
                        'lakelin-lemmings' => 'https://sl.cmdshft.com/whatarewedoing',
                        'spencer-hatcher' => 'https://sl.cmdshft.com/honkytonkhideaway',
                        '2-lane-summer' => 'https://sl.cmdshft.com/nogoingback',
                        'annie-bosko' => 'https://sl.cmdshft.com/californiacowgirlalbum',
                        'dusty-black' => 'https://sl.cmdshft.com/IDWBR',
                    ];
                @endphp
                <img class="ml-8 mb-4 max-w-64" src="{{ $releases[$artist->slug] }}" alt="">
                <a href="{{ $releaseLinks[$artist->slug] }}" class="ml-8 mb-4 flex justify-center">
                    <span class="block w-fit px-2 py-1 text-xl text-center bg-alt-white text-alt-black rounded">Available Now</span>
                </a>
            </div>
            <div class="text-alt-white lg:text-xl">{{$artist->about}}</div>
        </div>
    </div>
</x-layout>