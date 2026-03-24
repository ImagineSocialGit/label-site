<x-layout title="Manage News For {{ $artist->name }}" :universalData="$universalData">
    <div class="mt-64 mb-24 py-8 px-12 max-w-5xl w-full bg-white flex flex-col border-2 border-alt-black">
        @php
            $hasApiPosts = false;
            $onlyApi = true;
            foreach ($artist->posts as $post) {
                if ($post->external_site_id){
                    if (!$hasApiPosts) {
                        $hasApiPosts = true;
                    }
                } else {
                    $onlyApi = false;
                }
            }
        @endphp
        <div x-data="{hasApiPosts: {{$hasApiPosts}}, hideExternal: true}" class="py-8 px-12 max-w-5xl w-full bg-white flex flex-col divide-y divide-alt-black/50 border-2 border-alt-black">
            <div class="flex justify-center space-x-12 pb-8">
                <x-admin-button href="/admin-{{config('app.initials')}}/artists/{{$artist->slug}}/news/add">Add News</x-admin-button>
                @if (!$onlyApi)
                <form action="/admin-{{config('app.initials')}}/artists/{{$artist->slug}}/news/push" method="post">
                    @csrf
                    <button class="w-fit px-2 py-1 bg-alt-black border-2 border-alt-black rounded-lg text-white text-lg
                    hover:bg-white hover:text-alt-black duration-300 cursor-pointer">Push News Live</button>
                </form>
                @endif
                <button x-show="hasApiPosts" @click="hideExternal = !hideExternal" class="w-fit px-2 py-1 bg-alt-black border-2 border-alt-black rounded-lg text-white text-lg
                hover:bg-white hover:text-alt-black duration-300 cursor-pointer"><span x-text="hideExternal ? 'Show' : 'Hide'"></span> Posts From API</button>
            </div>
            <div class="w-full mx-auto bg-gray-100 rounded flex flex-col border border-black divide-y divide-black">
                <div class="w-full px-4 py-2 grid grid-cols-13 gap-4 border-b border-black">
                    <div class="col-span-7 text-lg">Title</div>
                    <div class="col-span-3 text-lg">Published</div>
                    <div class="col-span-1 text-lg">Live?</div>
                    <div class="col-span-2"></div>
                </div>
                @if (count($artist->posts) > 0)
                @foreach ($artist->posts as $post)
                @php
                    $post->title = preg_replace('/<[^>]*>/', '', $post->title);
                @endphp
                @if($loop->first && $onlyApi)
                    <div x-show="hideExternal" class="py-12 text-center">There is no news available at this time.</div>
                @endif
                <div x-data="{hide: {{ $post->external_site_id != null ? 1 : 0 }}}"
                    x-show="hide ? !hideExternal : !hide"
                    x-transition
                    class="w-full px-4 py-2 grid grid-cols-13 gap-4 border-b border-black">
                    <div class="col-span-7">{{$post->title}}</div>
                    <div class="col-span-3">{{ $post->publish_date }}</div>
                    @php
                        $text = 'No';
                        $greenText = false;
                        if ($post->isLive()){
                            $text = 'Yes';
                            $greenText = true;
                        } else if ($post->external_site_id){
                            $text = 'API';
                            $greenText = true;
                        }
                    @endphp
                    <div class="col-span-1 font-medium {{$greenText ? 'text-green-600' : 'text-red-600'}}">{{$text}}</div>
                    <div class="col-span-2 flex space-x-1 h-fit">
                        <a href="/admin-{{config('app.initials')}}/news/edit/{{ $post->id }}" class="underline">Edit</a>
                        <span>/</span>
                        <form action="/admin-{{config('app.initials')}}/news/delete/{{ $post->id }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button class="cursor-pointer" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
                @endforeach
                @else
                    <div class="py-12 text-center">There is no news available at this time.</div>
                @endif
            </div>
        </div>
    </div>
</x-layout>