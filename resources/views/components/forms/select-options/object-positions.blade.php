@php
    $objectPositions = [
        'object-center' => 'center',
        'object-top' => 'top',
        'object-top-left' => 'top-left',
        'object-top-right' => 'top-right',
        'object-bottom' => 'bottom',
        'object-bottom-left' => 'bottom-left',
        'object-bottom-right' => 'bottom-right',
        'object-left' => 'left',
        'object-right' => 'right'
        ];
@endphp
@foreach ($objectPositions as $key => $value)
    <option value="{{ $key }}">{{ucwords($value)}}</option>
@endforeach