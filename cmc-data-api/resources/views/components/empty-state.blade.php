@props([
    'icon' => '📋',
    'title' => 'Aucune donnée trouvée',
    'subtitle' => 'Modifiez vos filtres ou effectuez une nouvelle saisie.',
])

<div {{ $attributes->class(['empty-state']) }}>
    <div class="empty-icon">{{ $icon }}</div>
    <h3 class="empty-title">{{ __($title) }}</h3>
    <p class="empty-sub">{{ __($subtitle) }}</p>
</div>
