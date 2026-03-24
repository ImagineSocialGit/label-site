@if (session()->has('success'))
    <div
        x-data="{show:true}"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition
        class="fixed bottom-4 right-4 px-4 py-2 bg-white border-2 border-black text-black font-serif text-xl z-50"
    >
        <p>{{session()->get('success')}}</p>
    </div>
@endif
@if (session()->has('failure'))
    <div
        x-data="{show:true}"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition
        class="fixed bottom-4 right-4 px-4 py-2 bg-red-700 border-2 text-white font-serif text-xl z-50"
    >
        <p>{{session()->get('failure')}}</p>
    </div>
@endif