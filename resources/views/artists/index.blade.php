<x-layout title="Manage Artists" :universalData="$universalData">
    <div class="mt-64 mb-24 py-6 px-8 max-w-5xl w-full bg-white flex flex-col border-2 border-alt-black">
        <div class="max-w-5xl w-full flex flex-col divide-y divide-alt-black/50">
            <div class="grid grid-cols-14 py-1 px-1">
                <span class="col-span-2">Name</span>
                <span class="col-span-4">URL</span>
                <span class="col-span-1 text-center">Has API?</span>
                <span class="col-span-2 text-center">Get Socials</span>
                <span class="col-span-1 text-center">Music</span>
                <span class="col-span-1 text-center">News</span>
                <span class="col-span-1"></span>
                <span class="col-span-1"></span>
                <span class="col-span-1"></span>
            </div>
            @foreach ($artists as $artist)
            <div class="grid grid-cols-14 py-1 px-1">
                <span class="col-span-2 text-left"><a class="underline" href="/{{ $artist->slug }}">{{ $artist->name }}</a></span>
                <span class="col-span-4 text-left">{{ $artist->url }}</span>
                <span class="col-span-1 text-center">{{$artist->token ? 'Yes' : 'No'}}</span>
                <span class="col-span-2 text-center">Update</span>
                <span class="col-span-1 text-center"><a class="underline" href="/admin-{{ config('app.initials') }}/artists/{{ $artist->slug }}/music">Manage</a></span>
                <span class="col-span-1 text-center"><a class="underline" href="/admin-{{ config('app.initials') }}/artists/{{ $artist->slug }}/news">Manage</a></span>
                <span class="col-span-1 text-right"><a class="underline" href="/admin-{{ config('app.initials') }}/artists/edit/{{ $artist->slug }}">Edit</a></span>
                <form class="col-span-1 text-right" action="/admin-{{config('app.initials')}}/artists/{{ $artist->slug }}/pageStyles/push" method="post">
                    @csrf
                    @method('PATCH')
                        <button class="cursor-pointer underline" type="submit">Push</button>
                </form>
                <span class="col-span-1 text-right">Delete</span>
            </div>
            @endforeach
        </div>
    </div>
</x-layout>