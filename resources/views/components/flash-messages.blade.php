@props(['bag' => null, 'showErrors' => false])

@php
    $messageErrors = $bag ? $errors->getBag($bag) : $errors;
@endphp

<div class="feedback-region" aria-live="polite" aria-atomic="true">
    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" role="alert">{{ session('error') }}</div>
    @endif

    @if ($showErrors && $messageErrors->any())
        <div class="alert alert-error" role="alert">
            <strong>Please review the highlighted information.</strong>
            <ul>
                @foreach ($messageErrors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
