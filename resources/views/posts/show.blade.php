<x-layout title="{{ config('app.site-name') }} | {{ $post->title }}" :universalData="$universalData">

    <div class="relative lg:max-w-5xl xl:max-w-6xl mx-6 lg:mx-auto pt-56 lg:pt-72 pb-12 lg:pb-24">
        <div class="hidden lg:block space-y-8 text-alt-white">
            <div class="flex flex-col">
                <h1 class="max-w-4xl mx-auto text-4xl pb-4 font-semibold text-center font-title">{!! $post->title !!}</h1>
                <h2 class="max-w-3xl mx-auto text-xl py-4 text-center font-press">{!! $post->subtitle_one !!}</h2>
                <h3 class="max-w-3xl mx-auto text-lg pt-4 text-center font-press">{!! $post->subtitle_two !!}</h3>
            </div>
            <img class="max-w-md mx-auto w-full h-full" src="{{ $post->image }}" alt="">                
            <div class="space-y-3 pb-8 font-long-text text-lg">
                {!! $post->body !!}
            </div>
        </div>
        <div class="lg:hidden space-y-4 text-alt-white">
            <div class="flex flex-col divide-y-4">
                <h1 class="max-w-2xl mx-auto text-2xl pb-4 font-semibold text-center font-title">{!! $post->title !!}</h1>
                <h2 class="max-w-2xl mx-auto text-lg py-4 text-center font-subtitle">{!! $post->subtitle_one !!}</h2>
                <h3 class="max-w-2xl mx-auto text-base pt-4 text-center font-subtitle">{!! $post->subtitle_two !!}</h3>
            </div>            
            <img class="max-w-xs sm:max-w-sm md:max-w-md mx-auto w-full h-full" src="{{ $post->image }}" alt="">
            <div class="max-w-2xl mx-auto space-y-3 font-long-text pb-4">
                {!! $post->body !!}
            </div>
        </div>
    </div>
    
</x-layout>