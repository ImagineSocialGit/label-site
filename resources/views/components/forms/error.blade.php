@props(['name'])
@error($name)
<p class="text-white bg-red-700 p-1 rounded text-sm mt-1">
    {{ $message }}
</p>
@enderror