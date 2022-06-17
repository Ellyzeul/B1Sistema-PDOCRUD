@push('styles')
    <link rel="stylesheet" href="{{ asset('static/css/dropdown-list.css') }}">
@endpush

@props([
    'label',
    'options'
])

<div class="dropdown">
    <button class="dropbtn">{{ $label }}</button>
    <div class="dropdown-content">
        @foreach ($options as $option)
            <a href="{{ $option }}">0.0</a>
        @endforeach
    </div>
</div>
