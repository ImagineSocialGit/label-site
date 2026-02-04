<x-layout title="Edit Artist" :universalData="$universalData">
    <div class="bg-white w-full">{{var_dump($errors)}}</div>
    <div class="pt-72 w-full h-full max-w-3xl">
        <h1 class="text-center text-secondary text-4xl font-tai uppercase font-bold pb-12">Edit {{$artist->name}}</h1>

        <form class="flex flex-col space-y-4 pb-24"
            action="/admin-{{ config('app.initials') }}/artists/edit/{{ $artist->id }}"
            method="post"
            enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input class="px-2 py-1 bg-white" type="text" name="name" value="{{ $artist->name }}">
            <select class="px-2 py-1 bg-white" name="label_id" id="label_id">
                @foreach ($labels as $label)
                    <option value="{{ $label->id }}" {{ $artist->label_id == $label->id ? 'selected' : '' }}>{{$label->name}}</option>
                @endforeach            
            </select>
            <input class="px-2 py-1 bg-white" type="text" name="url" value="{{ $artist->url }}">
            <input class="px-2 py-1 bg-white" type="text" name="token" value="{{ $artist->token }}" placeholder="API Key" autocomplete="off">
            <input class="px-1 py-1 bg-white file:px-1 file:py-0.5 file:rounded file:border file:border-black file:bg-gray-200 hover:file:opacity-60 file:duration-200 cursor-pointer"
                type="file" name="desktop_image">
            <select class="px-2 py-1 bg-white" name="desktop_image_position">
                <option value="top">Top</option>
                <option value="center">Center</option>
                <option value="bottom">Bottom</option>
                <option value="left">Left</option>
                <option value="right">Right</option>
            </select>
            <input class="px-1 py-1 bg-white file:px-1 file:py-0.5 file:rounded file:border file:border-black file:bg-gray-200 hover:file:opacity-60 file:duration-200 cursor-pointer"
                type="file" name="mobile_image">
            <select class="px-2 py-1 bg-white" name="mobile_image_position">
                <option value="top">Top</option>
                <option value="center">Center</option>
                <option value="bottom">Bottom</option>
                <option value="left">Left</option>
                <option value="right">Right</option>
            </select>
            <textarea class="px-2 py-1 bg-white" placeholder="About" name="about" id="about" cols="30" rows="10">{{ $artist->about }}</textarea>

            <button class="px-2 py-1 bg-white rounded w-fit text-lg" type="submit">Update</button>
        </form>
    </div>
    
</x-layout>