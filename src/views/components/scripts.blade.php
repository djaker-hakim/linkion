@props([
    "alpine" => true
])
@php
    $src = $alpine ? "/linkionWithAlpine/script" : "/linkion/script";
@endphp
<script data-token="{{ csrf_token() }}" src="{{ $src }}" {{ $attributes }}></script>