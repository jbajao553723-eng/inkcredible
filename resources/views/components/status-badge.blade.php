@props(['status', 'label' => null])

@php
    $normalizedStatus = str($status)->lower()->replace(' ', '-')->toString();
    $statusClass = match ($normalizedStatus) {
        'approved', 'paid', 'completed', 'verified' => 'badge-success',
        'pending', 'partial', 'processing' => 'badge-warning',
        'rejected', 'failed', 'overdue', 'cancelled' => 'badge-danger',
        'active' => 'badge-purple',
        default => 'badge-neutral',
    };
@endphp

<span {{ $attributes->class(['badge', $statusClass]) }}>{{ $label ?? str($status)->replace(['_', '-'], ' ')->title() }}</span>
