<x-layout title="Manage Music For {{ $artist->name }}" :universalData="$universalData">
    <div class="mt-64 mb-24 py-8 px-12 max-w-5xl w-full bg-white flex flex-col border-2 border-alt-black">
        <div class="flex justify-center space-x-12 pb-8">
            <x-admin-button href="/admin-{{config('app.initials')}}/artists/{{$artist->slug}}/music/add">Add Music</x-admin-button>
        </div>
        <div class="max-w-5xl w-full mx-auto bg-gray-100 rounded flex flex-col border border-black divide-y divide-black">
            <div class="w-full px-4 py-2 flex space-x-8 justify-between border-b border-black">
                <div class="text-lg w-100">Title</div>
                <div class="text-lg w-25">Presave</div>
                <div class="text-lg w-25">Release</div>
                <div class="text-lg w-8">Live?</div>
                <div class="text-lg w-8">API?</div>
                <div class="w-24">Edit/Delete</div>
            </div>
            @if (count($musicItems) > 0)
            @foreach ($musicItems as $item)
            <div class="w-full px-4 py-2 flex space-x-8 justify-between border-b border-black">
                <div class="text-lg w-100">{{$item->title}}</div>
                <div class="text-lg w-25">{{ $item->presave }}</div>
                <div class="text-lg w-25">{{ $item->released }}</div>
                <div class="text-lg w-8 font-medium {{$item->live ? 'text-green-600' : 'text-red-600'}}">{{($item->live) ? 'Yes' : 'No'}}</div>
                <div class="text-lg w-8 font-medium {{$item->api_access ? 'text-green-600' : 'text-red-600'}}">{{$item->api_access ? 'Yes' : 'No'}}</div>
                <div class="w-24">
                    <a href="/admin-{{config('app.initials')}}/music/edit/{{ $item->id }}" class="underline">Edit</a>
                    /
                    <form action="/admin-{{config('app.initials')}}/artists/{{ $artist->slug }}/music/{{ $item->id }}/delete" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="cursor-pointer" type="submit">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
            @else
                <div class="py-12 text-center">There is no music available at this time.</div>
            @endif
        </div>
    </div>
</x-layout>