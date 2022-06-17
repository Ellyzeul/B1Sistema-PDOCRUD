@push('styles')
    <link rel="stylesheet" href="{{ asset('static/css/nav-bar.css') }}">
@endpush

@props([
    'labels',
    'optionslist'
])

@php
    $i = 0;
    var_dump($labels);
    $labelsarr = json_decode($labels);
    var_dump($labelsarr);
    $optionslist = json_decode($optionslist);
@endphp

<nav id="navbar">
    @while (isset($labelsarr[$i]))
        <x-dropdown-list
            label="{{ $labelsarr[$i] }}"
            options="{{ $optionslist[$i++] }}"
        />
    @endwhile
</nav>