<x-layout title="Add Music" :universalData="$universalData">
    <div class="mt-64 mb-24 py-8 px-12 max-w-5xl w-full bg-white flex flex-col border-2 border-alt-black">
        <form class="flex flex-col space-y-6" action="/admin-{{config('app.initials')}}/artists/{{ $artist->slug }}/music/add" method="post" enctype="multipart/form-data">
        @csrf
            <div class="text-center text-4xl">Add Some Music</div>
            <div class="">
                <x-forms.label>Title</x-forms.label>
                <x-forms.input class="w-full" name="title"/>
            </div>
            <div class="max-w-xl flex justify-between" x-data="{modifiedField: false}">
                <div>
                    <div class="text-2xl mb-2 w-full">Presave Date</div>
                    <input x-ref="preDate" @change="!modifiedField ? $refs.relDate.value = $el.value : ''" class="px-4 py-2 text-lg rounded-lg border-black border-2" name="presave_date" id="presave" type="date" value="{{date('Y-m-d')}}">
                    @error('presave')
                    <x-forms.error name="presave_date" />
                    @enderror
                    <ul class="pl-4 mt-2 text-sm list-disc w-72">
                        <li>If there is no Presave Date, ensure above field is the same as the Release Date</li>
                        <li>Othewise, the Presave Date MUST be set to precede the Release Date</li>
                    </ul>
                </div>
                <div>
                    <div class="text-2xl mb-2 w-full">Release Date</div>
                    <input x-ref="relDate" @change="modifiedField = true" class="px-4 py-2 text-lg rounded-lg border-black border-2" name="release_date" id="release-date" type="date" value="{{date('Y-m-d')}}">
                    <ul class="pl-4 mt-2 text-sm list-disc w-72">
                        <li>Setting BOTH the Presave Date and Released Date to a future date will hide the music from the public until midnight (Eastern Time) of the Presave Date.</li>
                    </ul>
                </div>
            </div>
            <div x-data="previewControl()" x-init="imagePreview = $refs.imageField; imageInput = $refs.imageInput" class="flex justify-between">
                <div class="">
                    <div class="text-2xl mb-2">Artwork</div>
                    <input
                        @change="setImage()"
                        class="px-4 py-2 w-2/3 text-lg rounded-lg border-2 border-black bg-white"
                        name="artwork"
                        type="file"
                        accept="image/*"
                        x-ref="imageInput"
                        value = "{{old('artwork')}}"
                        >
                    <x-forms.error name="artwork" />
                    <ul class="ml-8 text-md list-disc">
                        <li>Image file sizes cannot exceed 2mb; Recommended <1.0mb </li>
                        <li>The resolution should not exceed 1920x1920.</li>
                    </ul>
                </div>
                <div class="relative h-36 w-36 my-2 border border-black flex items-center justify-center">
                    <div x-show="!addedImage" class="text-sm px-2 text-center"><span>Please input image to see preview...</span></div>
                    <img x-show="addedImage" x-ref="imageField" class="max-w-xl h-36 object-contain w-full" src="#" alt="">
                </div>
            </div>
            <div x-data="previewControl()" x-init="imagePreview = $refs.imageField; imageInput = $refs.imageInput" class="space-y-2">
                <div class="">
                    <div class="text-2xl mb-2">Banner Image</div>
                    <input
                        @change="setImage()"
                        class="px-4 py-2 w-2/3 text-lg rounded-lg border-2 border-black bg-white"
                        name="banner_image"
                        type="file"
                        accept="image/*"
                        x-ref="imageInput"
                        value = "{{old('artwork')}}"
                        >
                    <x-forms.error name="banner_image" />
                    <ul class="ml-8 text-sm list-disc">
                        <li>Image file sizes cannot exceed 2mb; Recommended <1.0mb </li>
                        <li>Recommended aspect ratio for <span class="underline">banner images</span> is 11:4 (e.g. 550px wide, 200px tall).</li>
                        <li>The resolution of the largest side should not exceed 1920px (e.g. 1920x699).</li>
                    </ul>
                </div>
                <div class="relative aspect-banner w-full min-h-24 my-2 border border-black flex items-center justify-center">
                    <div x-show="!addedImage" class="text-sm px-2 text-center"><span>Please input image to see preview...</span></div>
                    <img x-show="addedImage" x-ref="imageField" class="w-full h-full object-contain" src="#" alt="">
                </div>
            </div>
            <div x-data="{mrl: 1, count: 0}" class="">
                <x-forms.radio name="is_multi_retailer_link" id="is-multi-retailer-link" model="mrl">Do you have a multi-retailer link?</x-forms.radio>
                <div x-show="mrl == 1">
                    <x-forms.label>link</x-forms.label>
                    <x-forms.input class="w-full" name="link" />
                </div>
                <div x-show="mrl == 0">
                    <template x-for="i in count">
                        <div class="flex space-x-4 pt-2">
                            <div class="flex space-x-2 items-center">
                            <x-forms.label>Service</x-forms.label>
                            <x-forms.input name="music_link[][service]" id="music-link-service" />
                            <div class="flex space-x-2 items-center">
                                <x-forms.label>Link</x-forms.label>
                                <x-forms.input name="music_link[][link]" id="music-link-link" />
                            </div>
                        </div>
                        <button @click.prevent="count--">Remove</div>
                    </template>
                    <button @click.prevent="count++">Add Service Link</button>
                </div>
            </div>
            <div x-data="{live: 0, holiday: 0}" class="flex flex-col space-y-6">
                <div class="flex space-x-12">
                    <x-forms.radio name="is_holiday_release" id="is-holiday-release" model="holiday">Holiday Release?</x-forms.radio>
                </div>
                <div class="flex space-x-12">
                    <x-forms.radio name="is_production" id="is-production" model="live">Make Live Immediately?</x-forms.radio>
                </div>
            </div>
            <div class="">
                <ul class="mb-2 ml-8 text-sm list-disc">
                    <li>Setting "Make Live Immediately" to "Yes" does NOT override the release date.</li>
                    <li>If "Make Live Immediately" is set to "No", you must select "Sync Music" to make the live site follow the publish schedule.</li>
                    <li>Setting "Allow API Access" to "Yes" allows associated sites (label, publishing, etc.) to retrieve the music item and automatically follow the release schedule.</li>
                    <li>Associated sites using the API will only retrieve the music items that are "Live".</li>
                </ul>
            </div>

            <div class="w-fit">
                <x-forms.button>Submit</x-forms.button>
            </div>    
        </form>
        <script>
            function previewControl(){
                return {
                    imageInput: null,
                    imagePreview: null,
                    mainImageSource: null,
                    addedImage: false,
                    setImage(){
                        const [file] = this.imageInput.files;
                        if (file){
                            this.imagePreview.src = URL.createObjectURL(file);
                            this.mainImageSource = this.imagePreview.src;
                            this.addedImage = true;
                        }
                    },
                }
            }
        </script>
    </div>
</x-layout>