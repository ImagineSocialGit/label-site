<label {{ $attributes->merge(['class' => 'mb-0.5']) }} {{ $attributes }}>
    {{ str_contains($slot, '_') ? ucwords(str_replace('_', ' ', $slot)) : ucwords($slot) }}
</label>