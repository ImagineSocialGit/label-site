<select {{ $attributes->merge(['class' => 'text-base px-1 py-0.5 bg-white border rounded', 'type' => 'text']) }} {{ $attributes }}>
{{ $slot }}
</select>