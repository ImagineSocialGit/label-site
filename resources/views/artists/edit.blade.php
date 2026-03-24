<x-layout title="Edit Artist" :universalData="$universalData">
    <div class="pt-72 pb-24 w-full h-full max-w-3xl">
        <h1 class="text-center text-secondary text-4xl font-tai uppercase font-bold pb-12">Edit {{$artist->name}}</h1>

        <form class="flex flex-col space-y-4 p-4 bg-white"
            action="/admin-{{ config('app.initials') }}/artists/edit/{{ $artist->id }}"
            method="post"
            enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <x-forms.input class="px-2 py-1 bg-white" type="text" name="name" value="{{ $artist->name }}" />
            <select class="px-2 py-1 bg-white border rounded" name="label_id" id="label_id">
                @foreach ($labels as $label)
                    <option value="{{ $label->id }}" {{ $artist->label_id == $label->id ? 'selected' : '' }}>{{$label->name}}</option>
                @endforeach            
            </select>
            <x-forms.input class="px-2 py-1 bg-white" type="text" name="url" value="{{ $artist->url }}" />
            <x-forms.input class="px-2 py-1 bg-white" type="text" name="token" value="{{ $artist->token }}" placeholder="API Key" autocomplete="off" />
            
            <div x-data="{desktopUseCustom: 0, mobileUseCustom: 0}" class="flex flex-col space-y-2">
                <div class="flex space-x-2">
                    <x-forms.radio name="desktop[image_use_custom_position]" id="desktop-image-use-custom-position" model="desktopUseCustom">Use Custom Position For Destkop?</x-forms.radio>
                    <x-forms.radio name="mobile[image_use_custom_position]" id="mobile-image-use-custom-position" model="mobileUseCustom">Use Custom Position For Mobile?</x-forms.radio>
                </div>

                <x-forms.input class="px-1 py-1 bg-white file:px-1 file:py-0.5 file:rounded file:border file:border-black file:bg-gray-200 hover:file:opacity-60 file:duration-200 cursor-pointer"
                    type="file" name="desktop[image]" />
                <select x-show="desktopUseCustom == 0" class="px-2 py-1 bg-white border rounded" name="desktop[image_position]">
                    <x-forms.select-options.object-positions />
                </select>

                <div x-show="desktopUseCustom == 1" class="">
                    <x-forms.input name="desktop[image_custom_position_x]" type="number" min="0" max="100" step="1" value="50" />
                    <x-forms.input name="desktop[image_custom_position_y]" type="number" min="0" max="100" step="1" value="50" />
                </div>
                <x-forms.input class="px-1 py-1 bg-white file:px-1 file:py-0.5 file:rounded file:border file:border-black file:bg-gray-200 hover:file:opacity-60 file:duration-200 cursor-pointer"
                    type="file" name="mobile[image]" />
                <select x-show="mobileUseCustom == 0" class="px-2 py-1 bg-white border rounded" name="mobile[image_position]">
                    <x-forms.select-options.object-positions />
                </select>
                <div x-show="mobileUseCustom == 1" class="">
                    <x-forms.input name="mobile[image_custom_position_x]" type="number" min="0" max="100" step="1" value="50" />
                    <x-forms.input name="mobile[image_custom_position_y]" type="number" min="0" max="100" step="1" value="50" />
                </div>
            </div>
            <textarea class="px-2 py-1 bg-white border rounded" placeholder="About" name="about" id="about" cols="30" rows="10">{{ $artist->about }}</textarea>

            <div class="w-fit">
                <x-forms.button>Update</x-forms.button>
            </div>
        </form>
    </div>
    
</x-layout>