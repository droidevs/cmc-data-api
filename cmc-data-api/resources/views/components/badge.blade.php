@props(['color' => 'gray'])

@php
    $classes = match ($color) {
        'indigo', 'Résidentiel' => 'badge-indigo',
        'green', 'Actif', 'Admis' => 'badge-green',
        'amber', 'Alternance', 'Vacataire', 'Redoublant' => 'badge-amber',
        'red', 'Inactif', 'Abandon' => 'badge-red',
        'navy', 'OFPPT' => 'badge-navy',
        default => 'badge-gray',
    };
@endphp

<span {{ $attributes->class(['badge', $classes]) }}>
    {{ $slot }}
</span>
