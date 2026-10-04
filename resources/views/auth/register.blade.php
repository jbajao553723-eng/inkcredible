<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>@include('partials.auth-styles') @include('partials.motion-styles')</style>
@vite('resources/js/app.js')
</head>

<body>
<main class="auth-page">
    <section class="brand-panel" aria-label="About Inkcredible">
        <a class="brand" href="{{ route('login') }}"><x-brand-mark /><span>Inkcredible</span></a>

        <div class="brand-content">
            <div class="brand-eyebrow">Get started</div>
            <h2 class="brand-title">A simpler way to manage your loan journey.</h2>
            <p class="brand-description">Create your account to request a loan, follow its approval status, and stay on top of every payment.</p>
            <ul class="feature-list">
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Quick online loan requests</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Real-time balance overview</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Clear transaction records</li>
            </ul>
        </div>

        <div class="brand-footer">Inkcredible Lending Management System</div>
    </section>

    <section class="form-panel">
        <div class="form-shell register">
            <a class="brand mobile-brand" href="{{ route('login') }}"><x-brand-mark /><span>Inkcredible</span></a>
            <div class="form-eyebrow">Client registration</div>
            <h1>Create your account</h1>
            <p class="form-description">Set up your profile to start submitting and managing loan requests.</p>

            @if($errors->any())
                <div class="alert alert-error" role="alert" tabindex="-1" data-error-summary>
                    Please review the highlighted fields and try again.
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="first-name">First name <span class="required-label">Required</span></label>
                        <input type="text" name="first_name" id="first-name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name') }}" placeholder="Juan" autocomplete="given-name" maxlength="100" @error('first_name') aria-invalid="true" aria-describedby="first-name-error" @enderror autofocus required>
                        @error('first_name')<p class="field-error" id="first-name-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="last-name">Last name <span class="required-label">Required</span></label>
                        <input type="text" name="last_name" id="last-name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name') }}" placeholder="Dela Cruz" autocomplete="family-name" maxlength="100" @error('last_name') aria-invalid="true" aria-describedby="last-name-error" @enderror required>
                        @error('last_name')<p class="field-error" id="last-name-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email address <span class="required-label">Required</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror required>
                    @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact-number">Contact number <span class="required-label">Required</span></label>
                        <input type="tel" name="contact_number" id="contact-number" class="form-control @error('contact_number') is-invalid @enderror"
                               value="{{ old('contact_number') }}" placeholder="09XX XXX XXXX" autocomplete="tel" inputmode="tel" maxlength="30" @error('contact_number') aria-invalid="true" aria-describedby="contact-number-error" @enderror required>
                        @error('contact_number')<p class="field-error" id="contact-number-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="age">Age <span class="required-label">Required</span></label>
                        <input type="number" name="age" id="age" class="form-control @error('age') is-invalid @enderror"
                               value="{{ old('age') }}" min="18" max="120" placeholder="Your age" inputmode="numeric" @error('age') aria-invalid="true" aria-describedby="age-error" @enderror required>
                        @error('age')<p class="field-error" id="age-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="street-address">Street / building no. <span class="required-label">Required</span></label>
                    <input type="text" name="street_address" id="street-address" class="form-control @error('street_address') is-invalid @enderror" value="{{ old('street_address') }}" placeholder="House no., building, or street" autocomplete="address-line1" maxlength="255" required>
                    @error('street_address')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-row" data-address-selects data-selected-city="{{ old('city_municipality') }}" data-selected-barangay="{{ old('barangay') }}">
                    <div class="form-group">
                        <label class="form-label" for="province">Province <span class="required-label">Required</span></label>
                        <select name="province" id="province" class="form-control @error('province') is-invalid @enderror" autocomplete="address-level1" required>
                            <option value="">Select province</option>
                            @foreach(config('philippine_locations') as $location)<option value="{{ $location }}" @selected(old('province') === $location)>{{ $location }}</option>@endforeach
                        </select>
                        @error('province')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="city-municipality">City / municipality <span class="required-label">Required</span></label>
                        <select name="city_municipality" id="city-municipality" class="form-control @error('city_municipality') is-invalid @enderror" autocomplete="address-level2" required disabled>
                            <option value="">Select province first</option>
                        </select>
                        @error('city_municipality')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="barangay">Barangay <span class="required-label">Required</span></label>
                    <select name="barangay" id="barangay" class="form-control @error('barangay') is-invalid @enderror" autocomplete="address-level3" required disabled>
                        <option value="">Select city or municipality first</option>
                    </select>
                    <p class="field-help" id="address-lookup-status" aria-live="polite">Choose your province to load its cities and municipalities.</p>
                    @error('barangay')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrap">
                            <input type="password" name="password" id="password" class="form-control password-input @error('password') is-invalid @enderror"
                                   placeholder="Create a password" autocomplete="new-password" aria-describedby="password-help @error('password') password-error @enderror" @error('password') aria-invalid="true" @enderror required>
                            <button class="password-toggle" type="button" data-toggle-password="password" aria-controls="password" aria-pressed="false" aria-label="Show password"><svg class="eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg><svg class="eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.7 6.1A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a17 17 0 0 1-2.1 2.8M6.6 6.6C4 8.3 2.5 12 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg></button>
                        </div>
                        <div class="password-meter" data-password-meter data-strength="0" aria-hidden="true"><span></span><span></span><span></span></div>
                        <p class="field-help" id="password-help" data-password-help>Use at least 8 characters with a number or special character.</p>
                        @error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password-confirmation">Confirm password</label>
                        <div class="input-wrap">
                            <input type="password" name="password_confirmation" id="password-confirmation" class="form-control password-input"
                                   placeholder="Repeat your password" autocomplete="new-password" aria-describedby="password-match" required>
                            <button class="password-toggle" type="button" data-toggle-password="password-confirmation" aria-controls="password-confirmation" aria-pressed="false" aria-label="Show password confirmation"><svg class="eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg><svg class="eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.7 6.1A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a17 17 0 0 1-2.1 2.8M6.6 6.6C4 8.3 2.5 12 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg></button>
                        </div>
                        <p class="field-feedback" id="password-match" data-password-match aria-live="polite"></p>
                    </div>
                </div>

                <label class="consent-box">
                    <input type="checkbox" name="terms_accepted" value="1" @checked(old('terms_accepted')) required>
                    <span>I confirm that my information is accurate and I have read and agree to the <a href="{{ route('terms') }}" target="_blank" rel="noopener">Terms and Conditions</a>, including the privacy, acceptable-use, and account provisions.</span>
                </label>
                @error('terms_accepted')<p class="field-error" style="margin-top:-12px; margin-bottom:16px">You must accept the Terms and Conditions to create an account.</p>@enderror

                <button class="submit-button" type="submit" id="submit-register"><span>Create account</span></button>
                <p class="terms-note">Terms version {{ config('legal.account_terms_version') }} · Your acceptance date is recorded.</p>
            </form>

            <div class="divider">Already registered?</div>
            <p class="auth-switch">Return to your account securely. <a class="text-link" href="{{ route('login') }}">Sign in</a></p>
        </div>
    </section>
</main>

<script>
document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.togglePassword);
        const showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        this.classList.toggle('is-showing', !showing);
        this.setAttribute('aria-pressed', String(!showing));
        this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
});

const passwordInput = document.getElementById('password');
const confirmationInput = document.getElementById('password-confirmation');
const passwordMeter = document.querySelector('[data-password-meter]');
const passwordHelp = document.querySelector('[data-password-help]');
const passwordMatch = document.querySelector('[data-password-match]');

const updatePasswordFeedback = () => {
    const value = passwordInput.value;
    let strength = 0;

    if (value.length >= 8) strength++;
    if (/[A-Za-z]/.test(value) && /[\d\W_]/.test(value)) strength++;
    if (value.length >= 12 || /[^A-Za-z0-9]/.test(value)) strength++;

    passwordMeter.dataset.strength = value ? String(strength) : '0';
    passwordHelp.textContent = value
        ? ['Use at least 8 characters with a number or special character.', 'Good start - add a number or special character.', 'Password meets the required policy.', 'Strong password.'][strength]
        : 'Use at least 8 characters with a number or special character.';

    if (!confirmationInput.value) {
        passwordMatch.textContent = '';
        passwordMatch.className = 'field-feedback';
        return;
    }

    const matches = value === confirmationInput.value;
    passwordMatch.textContent = matches ? 'Passwords match.' : 'Passwords do not match yet.';
    passwordMatch.className = `field-feedback ${matches ? 'is-valid' : 'is-invalid'}`;
};

passwordInput.addEventListener('input', updatePasswordFeedback);
confirmationInput.addEventListener('input', updatePasswordFeedback);
document.querySelector('[data-error-summary]')?.focus({ preventScroll: true });

const addressContainer = document.querySelector('[data-address-selects]');
const provinceSelect = document.getElementById('province');
const citySelect = document.getElementById('city-municipality');
const barangaySelect = document.getElementById('barangay');
const addressStatus = document.getElementById('address-lookup-status');
const selectedCity = addressContainer?.dataset.selectedCity || '';
const selectedBarangay = addressContainer?.dataset.selectedBarangay || '';
const psgcBaseUrl = 'https://psgc.cloud/api/v2';

const responseItems = (payload) => Array.isArray(payload) ? payload : (payload.data || []);

const setAddressOptions = (select, items, placeholder, selectedValue = '') => {
    select.innerHTML = '';
    select.append(new Option(placeholder, ''));
    items
        .filter((item) => item.name && item.code)
        .sort((left, right) => left.name.localeCompare(right.name))
        .forEach((item) => {
            const option = new Option(item.name, item.name, false, item.name === selectedValue);
            option.dataset.code = item.code;
            select.append(option);
        });
    select.disabled = false;
};

const loadBarangays = async (cityCode, selectedValue = '') => {
    barangaySelect.disabled = true;
    barangaySelect.innerHTML = '<option value="">Loading barangays...</option>';
    addressStatus.textContent = 'Loading barangays...';

    try {
        const response = await fetch(`${psgcBaseUrl}/cities-municipalities/${encodeURIComponent(cityCode)}/barangays`, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Barangays could not be loaded.');
        setAddressOptions(barangaySelect, responseItems(await response.json()), 'Select barangay', selectedValue);
        addressStatus.textContent = 'Select the barangay for your current residential address.';
    } catch (error) {
        barangaySelect.innerHTML = '<option value="">Unable to load barangays</option>';
        addressStatus.textContent = 'Location service is unavailable. Select the city again to retry.';
    }
};

const loadCities = async (province, cityValue = '', barangayValue = '') => {
    citySelect.disabled = true;
    barangaySelect.disabled = true;
    citySelect.innerHTML = '<option value="">Loading cities...</option>';
    barangaySelect.innerHTML = '<option value="">Select city or municipality first</option>';
    addressStatus.textContent = 'Loading cities and municipalities...';

    const endpoint = province === 'Metro Manila'
        ? `${psgcBaseUrl}/regions/1300000000/cities-municipalities`
        : `${psgcBaseUrl}/provinces/${encodeURIComponent(province)}/cities-municipalities`;

    try {
        const response = await fetch(endpoint, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Cities could not be loaded.');
        const cities = responseItems(await response.json()).filter((item) => item.type !== 'SubMun');
        setAddressOptions(citySelect, cities, 'Select city or municipality', cityValue);
        addressStatus.textContent = 'Choose a city or municipality to load its barangays.';

        const selectedOption = citySelect.selectedOptions[0];
        if (cityValue && selectedOption?.dataset.code) {
            await loadBarangays(selectedOption.dataset.code, barangayValue);
        }
    } catch (error) {
        citySelect.innerHTML = '<option value="">Unable to load cities</option>';
        addressStatus.textContent = 'Location service is unavailable. Select the province again to retry.';
    }
};

provinceSelect?.addEventListener('change', () => {
    if (!provinceSelect.value) {
        citySelect.disabled = true;
        barangaySelect.disabled = true;
        citySelect.innerHTML = '<option value="">Select province first</option>';
        barangaySelect.innerHTML = '<option value="">Select city or municipality first</option>';
        addressStatus.textContent = 'Choose your province to load its cities and municipalities.';
        return;
    }

    loadCities(provinceSelect.value);
});

citySelect?.addEventListener('change', () => {
    const cityCode = citySelect.selectedOptions[0]?.dataset.code;
    if (cityCode) loadBarangays(cityCode);
});

if (provinceSelect?.value) loadCities(provinceSelect.value, selectedCity, selectedBarangay);

document.getElementById('register-form').addEventListener('submit', function () {
    const button = document.getElementById('submit-register');
    button.disabled = true;
    button.querySelector('span').textContent = 'Creating account…';
});
</script>
</body>
</html>
