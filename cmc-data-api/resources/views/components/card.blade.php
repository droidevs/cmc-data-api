@props([
    'title' => null,
])

<div {{ $attributes->class(['card']) }}>
    @if($title || isset($header))
        <div class="card-header">
            @if(isset($header))
                {{ $header }}
            @else
                <span class="card-title">{{ __($title) }}</span>
            @endif
        </div>
    @endif
    <div class="card-body" {{ isset($bodyAttributes) ? $bodyAttributes : '' }}>
        {{ $slot }}
    </div>
</div>
