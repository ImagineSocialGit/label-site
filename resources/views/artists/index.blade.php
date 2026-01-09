<x-layout title="Manage Artists" :universalData="$universalData">
    <div class="py-8 px-12 max-w-5xl w-full bg-white flex flex-col divide-y divide-alt-black/50 border-2 border-alt-black">
        <div class="grid grid-cols-12">
            <span class="col-span-3">Name</span>
            <span class="col-span-5">URL</span>
            <span class="col-span-2">Needs Refresh?</span>
            <span class="col-span-1">Edit</span>
            <span class="col-span-1">Delete</span>
        </div>
        @foreach ($artists as $artist)
        <div class="grid grid-cols-12">
            <span class="col-span-3">{{ $artist->name }}</span>
            <span class="col-span-5">{{ $artist->url }}</span>
            <span class="col-span-2"></span>
            <span class="col-span-1"><a class="underline" href="/admin-{{ config('app.initials') }}/artists/edit/{{ $artist->id }}">Edit</a></span>
            <span class="col-span-1">Delete</span>
        </div>
        @endforeach
    </div>
</x-layout>