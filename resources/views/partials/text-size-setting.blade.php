@php
    $selectedTextSize = old('text_size', data_get(auth()->user()?->ui_preferences, 'text_size', 'normal'));
@endphp
<fieldset class="text-size-setting" data-text-size-setting>
    <legend>Text size</legend>
    <p id="text-size-help">Choose a comfortable reading size. Preview it now, then save to use it throughout your account on any device.</p>
    <div class="text-size-options">
        @foreach(['small' => 'Smaller', 'normal' => 'Default', 'large' => 'Larger', 'extra-large' => 'Largest'] as $value => $label)
            <label class="text-size-option">
                <input type="radio" name="text_size" value="{{ $value }}" @checked($selectedTextSize === $value) aria-describedby="text-size-help">
                <span><strong aria-hidden="true" class="text-size-sample text-size-sample-{{ $value }}">Aa</strong>{{ $label }}</span>
            </label>
        @endforeach
    </div>
    <div class="text-size-preview" aria-live="polite"><strong>Comfortable reading</strong><p>Your balances, forms, and navigation adjust to your preferred text size.</p><span data-text-size-status>Current size: {{ ['small' => 'Smaller', 'normal' => 'Default', 'large' => 'Larger', 'extra-large' => 'Largest'][$selectedTextSize] ?? 'Default' }}</span></div>
</fieldset>
