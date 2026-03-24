<div x-data="{showTip: false}" {{ $attributes->merge(['class' => 'relative h-12 flex flex-col space-y-0.5 items-start']) }}>
    @if (isset($tip))
        <x-forms.tip>{{$tip}}</x-forms.tip>        
    @endif
    <span @mouseenter="showTip=true" @mouseleave="showTip=false" class="text-center cursor-default">
        {{$slot}}
    </span>
    <div class="flex space-x-1 items-center">
        <input type="radio" name="{{ $name }}" id="{{ $id }}" value={{ $altTrueValue ?? 1}} {{ isset($model) ? 'x-model=' . $model : ''}} {{ isset($changeTrue) ? '@change=' . $changeTrue : '' }}>
        <label class="text-sm" for="{{ $name }}">{{ $trueValue ?? 'Yes' }}</label>
        <span class="mx-0.5"></span>
        <input type="radio" name="{{ $name }}" id="{{ $id }}" value={{ $altFalseValue ?? 0}} {{ isset($model) ? 'x-model=' . $model : ''}} {{ isset($changeFalse) ? '@change=' . $changeFalse : '' }}>
        <label class="text-sm" for="{{ $name }}">{{ $falseValue ?? 'No' }}</label>
    </div>
</div>