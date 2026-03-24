<x-layout title="Add News" :universalData="$universalData">
    <div class="mt-64 mb-24 py-8 px-12 max-w-5xl w-full bg-white flex flex-col border-2 border-alt-black">
    <form action="/admin-{{ config('app.initials') }}/artists/{{ $artist->slug }}/news/add"
        x-data="checkLiveItems({{ json_encode($artist->music) }}, {{ json_encode($artist->videos) }})"
        method="post"
        enctype="multipart/form-data"
        class="flex flex-col space-y-6">
        @csrf
        <div x-show="displayWarning" x-transition class="fixed bg-black/80 top-0 bottom-0 left-0 w-full h-full flex items-center justify-center z-50">
            <div class="bg-white max-w-xl py-6 px-12 flex flex-col space-y-2">
                <span>The selected linked item is NOT live.</span>
                <span>Are you sure you want to add this news?</span>
                <div class="flex space-x-4">
                    <x-forms.button type="submit">Yes</x-forms.button>
                    <button @click.prevent="displayWarning = false" class="text-xl text-alt-black w-fit px-2 py-1 border border-alt-black rounded hover:text-white hover:bg-alt-black duration-300 cursor-pointer">No</button>
                </div>  
            </div>
        </div>
        <div class="flex flex-col space-y-1">
            <label class="text-xl pl-3" for="title">Title</label>
            <input class="w-full border border-gray-400 rounded px-1 py-0.5" type="text" name="title">
        </div>        
        <div class="flex flex-col space-y-1">
            <label class="text-xl pl-3" for="title">Subtitle One</label>
            <ul class="pl-8 text-sm list-disc">
                <li>Not required</li>
                <li>Appears under title, smaller font size</li>
                <li>Links allowed using the following format: &lt;link="[LINK HERE]"&gt;[TEXT HERE]&lt;/link&gt;</li>
                <li>Example: &lt;link="https://sl.cmdshft.com/musiclink"&gt;Amazing Single "New Song" OUT NOW&lt;/link&gt; </li>
            </ul>
            <textarea class="border border-gray-400 rounded px-1 py-0.5" name="subtitle_one" id="subtitle-one" cols="30" rows="2"></textarea>
        </div>        
        <div class="flex flex-col space-y-1">
            <label class="text-xl pl-3" for="title">Subtitle Two</label>
            <ul class="pl-8 text-sm list-disc">
                <li>Not required</li>
                <li>Appears under subtitle one, smaller font size</li>
                <li>Links Allowed</li>
            </ul>
            <textarea class="border border-gray-400 rounded px-1 py-0.5" name="subtitle_two" id="subtitle-two" cols="30" rows="2"></textarea>
        </div>
        <div class="flex flex-col space-y-2">
            <div class="flex flex-col space-y-2">
                <x-forms.select name="linked_item" id="linked-item" x-model="linkedItem">
                    <option value="none">None</option>
                    @if(count($artist->music) > 0)
                    <option value="music">Music</option>
                    @endif
                    @if(count($artist->videos) > 0)
                    <option value="video">Video</option>
                    @endif
                </x-forms.select>
                <div x-show="linkedItem == 'music'" class="flex space-x-4">
                    <x-forms.label class="text-base" for="music_id">Music</x-forms.label>
                    <x-forms.select
                        class="h-fit"
                        name="music_id"
                        id="music-id"
                        x-model="selectedMusic"
                        >
                        @foreach ($artist->music as $item)
                        <option value={{ $item->id }}>{{$item->title}}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div x-show="linkedItem == 'video'" class="flex space-x-4">
                    <x-forms.label class="text-base" for="video_id">Video</x-forms.label>
                    <x-forms.select
                        class="h-fit"
                        name="video_id"
                        id="video-id"
                        x-model="selectedVideo"
                        >
                        @foreach ($artist->videos as $item)
                        <option value="{{ $item->id }}">{{$item->title}}</option>
                        @endforeach
                    </x-forms.select>
                </div>
            </div>
            <div x-show="linkedItem == 'none'" x-data="previewControl()" x-init="imagePreview = $refs.imageField; imageInput = $refs.imageInput" class="flex justify-between">
                <div class="flex flex-col space-y-1">
                    <label class="text-xl pl-3" for="title">Image</label>
                    <input @change="setImage()"
                        accept="image/*"
                        x-ref="imageInput"
                        value = "{{old('image')}}"
                        class="w-64 border border-gray-400 rounded px-1 py-1 file:px-1 file:py-0.5 file:rounded file:border file:border-black file:bg-gray-200 hover:file:opacity-60 file:duration-200 cursor-pointer"
                        type="file"
                        name="image">
                    <div>
                        <x-forms.label for="image_position">Image Position</x-forms.label>
                        <x-forms.select
                            name="image_position"
                            id="image-position"
                            >
                            <x-forms.select-options.object-positions />                            
                        </x-forms.select>
                    </div>
                </div>
                <div class="relative h-36 w-36 my-2 border border-black flex items-center justify-center">
                    <div x-show="!addedImage" class="text-sm px-2 text-center"><span>Please input image to see preview...</span></div>
                    <img x-show="addedImage" x-ref="imageField" class="max-w-xl h-36 object-contain w-full" src="#" alt="">
                </div>
            </div>

        </div>
        <div x-data="modifyText()" class="flex flex-col space-y-1">
            <label class="text-xl pl-3" for="title">Content</label>
            <ul class="pl-8 text-sm list-disc">
                <li>Required</li>
                <li>Links Allowed</li>
                <li>You can use <u><a href="https://www.w3schools.com/html/html_formatting.asp">simple HTML formatting tags</a></u>, like &lt;b&gt;bold&lt;/b&gt; for <b>bold</b> text and &lt;i&gt;italicized&lt;/i&gt; for <i>italicized</i> text</li>
                <li>You can use &lt;numberlist&gt;[TEXT]&lt;/numberlist&gt; for a list with numbers, &lt;bullet&gt;[TEXT]&lt;/bullet&gt; for a list with &bull;, and &lt;tour&gt;[TEXT]&lt;/tour&gt; for a list with no symbols to use for things like tour dates</li>
            </ul>
            <div class="flex space-x-2">
                <div @click="makeLink()" class="my-1 px-1 py-0.5 border rounded cursor-pointer hover:opacity-60 duration-200 w-fit">Link</div>
                <div @click="makeBold()" class="my-1 px-1 py-0.5 border rounded cursor-pointer hover:opacity-60 duration-200 w-fit">Bold</div>
                <div @click="makeItalic()" class="my-1 px-1 py-0.5 border rounded cursor-pointer hover:opacity-60 duration-200 w-fit">Italic</div>
            </div>
            <textarea
                @keydown.window.prevent.ctrl.b="wrapTag('b')"
                @keydown.window.prevent.ctrl.i="wrapTag('i')"
                @keydown.window.prevent.ctrl.l="wrapLink()"
                class="border border-gray-400 rounded px-1 py-0.5"
                name="body"
                id="body"
                cols="30"
                rows="10"
                x-model="fieldValue"
            ></textarea>
            <script>
            function modifyText(){
                return {
                    fieldValue: '',

                    wrapTag(tag){
                        const el = document.activeElement;
                        if (!el || el.tagName.toLowerCase() !== 'textarea') return;

                        const start = el.selectionStart;
                        const end = el.selectionEnd;

                        if (start === end) return; // nothing selected

                        const text = el.value;
                        const selected = text.slice(start, end);

                        const openTag = `<${tag}>`;
                        const closeTag = `</${tag}>`;

                        const newText =
                            text.slice(0, start) +
                            openTag + selected + closeTag +
                            text.slice(end);

                        el.value = newText;

                        // keep selection inside the tags
                        el.selectionStart = start + openTag.length;
                        el.selectionEnd = end + openTag.length;
                    },

                    wrapLink(){
                        const el = document.activeElement;
                        if (!el || el.tagName.toLowerCase() !== 'textarea') return;

                        const start = el.selectionStart;
                        const end = el.selectionEnd;

                        if (start === end) return;

                        const text = el.value;
                        const selected = text.slice(start, end);

                        const openTag = `<link="">`;
                        const closeTag = `</link>`;

                        const newText =
                            text.slice(0, start) +
                            openTag + selected + closeTag +
                            text.slice(end);

                        el.value = newText;

                        el.selectionStart = start + openTag.length;
                        el.selectionEnd = end + openTag.length;
                    }
                }
            }
            </script>
        </div>
        <div class="flex justify-between items-start">
            <div class="w-1/2">
                <div class="text-xl mb-2 ">Date</div>
                <input class="px-4 py-2 rounded-lg border-black border-2" name="publish_date" type="date" value="{{date('Y-m-d')}}">
                <ul class="mb-2 ml-6 text-sm list-disc ">
                    <li>Automatically set to the current date</li>
                    <li>Setting to a future date/time will hide from the public until that time passes.</li>
                </ul>
            </div>
            <div class="w-1/2">
                <div class="text-xl mb-2">Time</div>
                <input type="time" class="px-4 py-2 rounded-lg border-black border-2" name="publish_time" value="00:00">
                <ul class="mb-2 ml-6 text-sm list-disc">
                    <li>Automatically set to midnight</li>
                </ul>
            </div>
        </div>
        <div x-data="{hide: 0, live: 0, api: 0}" class="flex space-x-12">
            <x-forms.radio name="is_production" id="is-production" model="live">Make Live Immediately?</x-forms.radio>
        </div>
        <div class="">
            <ul class="mb-2 ml-8 text-sm list-disc">
                <li>Setting "Make Live Immediately" to "Yes" does NOT override the published date/time.</li>
                <li>If "Make Live Immediately" is set to "No", you must select "Sync News" to make the live site follow the publish schedule.</li>
                <li>Setting "Allow API Access" to "Yes" allows associated sites (label, publishing, etc.) to retrieve the news item and automatically follow the publish schedule.</li>
                <li>Associated sites using the API will only retrieve the news items that are "Live".</li>
            </ul>
        </div>
        <button @click.prevent="checkSubmit($el)" class="text-xl text-alt-black w-fit px-2 py-1 border border-alt-black rounded hover:text-white hover:bg-alt-black duration-300 cursor-pointer" type="submit">Add News</button>
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
        function checkLiveItems(music, videos){
            return {
                displayWarning: false,
                linkedItem: 'none',
                music: music,
                videos: videos,
                selectedMusic: 1,
                selectedVideo: 1,
                checkSubmit(element){
                    if (this.linkedItem == 'none'){
                        element.parentElement.submit();
                    }

                    if (this.linkedItem == 'music'){
                        if (this.checkIsMusicLive()){
                            element.parentElement.submit();
                        } else {
                            this.displayWarning = true;
                        }
                    }

                    if (this.linkedItem == 'video'){
                        if (this.checkIsVideoLive()){
                            element.parentElement.submit();
                        } else {
                            this.displayWarning = true;
                        }
                    }

                },
                checkIsMusicLive(){
                    const result = this.music.find(({ id }) => id == this.selectedMusic);

                    if (result && result.live) {
                        return true;
                    } else {
                        return false;
                    }
                },
                checkIsVideoLive(){
                    const result = this.videos.find(({ id }) => id == this.selectedVideo);

                    if (result && result.live) {
                        return true;
                    } else {
                        return false;
                    }
                }
                
            }
        }
    </script>
</x-layout>