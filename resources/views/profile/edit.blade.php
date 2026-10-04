<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Personal Profile | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/app.js')
<style>
@include('partials.client-portal-styles')
@include('partials.settings-styles')

.settings-shell { max-width:1080px; }
.main { background:radial-gradient(circle at 92% 2%,rgba(99,102,241,.07),transparent 26%),var(--canvas); }
.profile-layout { display:grid; grid-template-columns:280px minmax(0,1fr); gap:20px; align-items:start; }
.profile-summary { position:sticky; top:24px; overflow:hidden; }
.profile-cover { height:76px; background:linear-gradient(135deg,#312e81,#4f46e5 58%,#7c3aed); }
.profile-summary-body { padding:0 20px 20px; }
.profile-avatar-wrap { position:relative; width:68px; height:68px; margin-top:-34px; }
.profile-avatar { display:grid; place-items:center; width:68px; height:68px; overflow:hidden; color:#4338ca; background:#fff; border:4px solid #fff; border-radius:18px; box-shadow:0 8px 20px rgba(16,24,40,.12); font-size:20px; font-weight:700; }
.profile-avatar img { width:100%; height:100%; object-fit:cover; }
.profile-avatar [hidden] { display:none; }
.photo-upload { position:absolute; right:-7px; bottom:-7px; display:grid; place-items:center; width:28px; height:28px; color:#fff; background:#4f46e5; border:3px solid #fff; border-radius:50%; box-shadow:0 4px 10px rgba(79,70,229,.25); cursor:pointer; transition:transform .16s ease,background-color .16s ease; }
.photo-upload:hover { background:#4338ca; transform:scale(1.06); }
.photo-upload svg { width:13px; height:13px; }
.profile-photo-input { position:absolute; width:1px; height:1px; overflow:hidden; opacity:0; pointer-events:none; }
.photo-help { margin-top:10px; color:#98a2b3; font-size:9px; line-height:1.45; }
.photo-help strong { display:block; overflow:hidden; color:#475467; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
.profile-name { margin:14px 0 0; font-size:17px; letter-spacing:-.02em; }
.profile-email { margin-top:5px; overflow:hidden; color:#667085; font-size:11px; text-overflow:ellipsis; white-space:nowrap; }
.profile-status { margin-top:12px; }
.completion-block { margin-top:20px; padding-top:18px; border-top:1px solid #f2f4f7; }
.completion-copy { display:flex; align-items:center; justify-content:space-between; gap:12px; color:#475467; font-size:10px; font-weight:600; }
.completion-copy strong { color:#4338ca; font-size:11px; }
.completion-track { height:7px; margin-top:9px; overflow:hidden; background:#eaecf0; border-radius:999px; }
.completion-fill { height:100%; background:linear-gradient(90deg,#6366f1,#8b5cf6); border-radius:inherit; }
.profile-facts { display:grid; gap:12px; margin-top:18px; }
.profile-fact { display:flex; align-items:flex-start; justify-content:space-between; gap:14px; }
.profile-fact span { color:#98a2b3; font-size:10px; }
.profile-fact strong { color:#344054; font-size:10px; font-weight:600; text-align:right; }
.verification-link { display:inline-flex; margin-top:18px; color:#4338ca; font-size:11px; font-weight:600; text-decoration:none; }
.profile-form-panel { border-color:#dfe3ea; box-shadow:0 8px 24px rgba(16,24,40,.045); }
.profile-form-panel .panel-header { align-items:flex-start; }
.required-note { color:#98a2b3; font-size:10px; white-space:nowrap; }
.profile-form { padding:0; }
.profile-form-section { padding:22px 24px 4px; }
.profile-form-section + .profile-form-section { border-top:1px solid #f2f4f7; }
.form-section-head { display:flex; gap:11px; margin-bottom:19px; }
.form-section-icon { display:grid; place-items:center; width:34px; height:34px; flex:0 0 34px; color:#4f46e5; background:#eef2ff; border-radius:9px; }
.form-section-icon svg { width:17px; height:17px; }
.form-section-title { margin:1px 0 0; color:#344054; font-size:12px; font-weight:700; }
.form-section-note { margin:4px 0 0; color:#98a2b3; font-size:10px; line-height:1.45; }
.form-control { transition:border-color .16s ease,box-shadow .16s ease,background-color .16s ease; }
.form-control:hover { border-color:#b8c0cc; }
.form-control[aria-invalid="true"] { border-color:#f04438; }
.form-actions { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:17px 24px; background:#fcfcfd; border-top:1px solid #f2f4f7; }
.save-state { display:flex; align-items:center; gap:7px; color:#98a2b3; font-size:10px; }
.save-state::before { width:7px; height:7px; background:#12b76a; border-radius:50%; content:''; }
.save-state.is-dirty { color:#b54708; }
.save-state.is-dirty::before { background:#f79009; }
.save-button { min-width:142px; }
@media(max-width:820px){.profile-layout{grid-template-columns:1fr}.profile-summary{position:static}.profile-summary-body{display:grid;grid-template-columns:auto minmax(0,1fr);column-gap:16px}.profile-avatar-wrap{grid-row:1/4}.photo-help,.profile-summary .field-error,.completion-block,.profile-facts,.verification-link{grid-column:1/-1}}
@media(max-width:560px){.profile-form-section{padding:19px 18px 3px}.form-actions{align-items:stretch;flex-direction:column;padding:15px 18px}.save-button{width:100%}.profile-summary-body{display:block}}
</style>
</head>
<body>
@php
    $profileValues = collect([
        $user->first_name,
        $user->last_name,
        $user->email,
        $user->contact_number,
        $user->age,
        $user->address,
    ]);
    $completedFields = $profileValues->filter(fn ($value) => filled($value))->count();
    $profileCompletion = (int) round(($completedFields / $profileValues->count()) * 100);
    $verificationStatus = $user->clientVerification?->status ?? 'not submitted';
    $verificationClass = match ($verificationStatus) {
        'approved' => 'badge-success',
        'pending' => 'badge-warning',
        'rejected' => 'badge-danger',
        default => 'badge-neutral',
    };
    $initials = collect(preg_split('/\s+/', trim($user->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
        ->implode('');
@endphp

@include('partials.client-sidebar', ['active' => 'settings'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell settings-shell">
    <header class="topbar">
        <div><div class="eyebrow">Account settings</div><h1>Personal profile</h1><p class="subtitle">Update the same identity and address details used at registration. Saved changes appear across your dashboard and future applications.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('dashboard') }}">Back to dashboard</a></div>
    </header>

    @include('partials.settings-tabs', ['activeSettings' => 'profile'])

    @if(session('status') === 'profile-updated')
        <div class="alert alert-success" role="status">Your personal profile was updated successfully.</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" role="alert"><strong>Please review the highlighted fields.</strong></div>
    @endif

    <div class="profile-layout">
        <aside class="panel profile-summary">
            <div class="profile-cover"></div>
            <div class="profile-summary-body">
                <div class="profile-avatar-wrap">
                    <div class="profile-avatar"><img id="profile-photo-preview" src="{{ $user->profile_photo_path ? route('profile.photo', ['v' => $user->updated_at?->timestamp]) : '' }}" alt="{{ $user->name }} profile photo" @if(! $user->profile_photo_path) hidden @endif><span id="profile-photo-initials" @if($user->profile_photo_path) hidden @endif>{{ $initials ?: 'U' }}</span></div>
                    <label class="photo-upload" for="profile-photo" title="Choose a profile picture" aria-label="Choose a profile picture"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 8h3l1.5-2h7L17 8h3v11H4z"/><circle cx="12" cy="13" r="3.5" stroke-width="1.8"/></svg></label>
                </div>
                <input class="profile-photo-input" id="profile-photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp" form="profile-form">
                <h2 class="profile-name">{{ $user->name }}</h2>
                <div class="profile-email">{{ $user->email }}</div>
                <div class="profile-status"><span class="badge {{ $verificationClass }}">Verification: {{ $verificationStatus }}</span></div>
                <div class="photo-help" id="profile-photo-help"><strong>Change profile picture</strong>JPG, PNG, or WebP up to 2 MB.</div>
                @error('profile_photo')<p class="field-error">{{ $message }}</p>@enderror

                <div class="completion-block">
                    <div class="completion-copy"><span>Profile completeness</span><strong>{{ $profileCompletion }}%</strong></div>
                    <div class="completion-track"><div class="completion-fill progress-bar" style="width:{{ $profileCompletion }}%"></div></div>
                </div>

                <div class="profile-facts">
                    <div class="profile-fact"><span>Email</span><strong>{{ $user->email_verified_at ? 'Verified' : 'Not verified' }}</strong></div>
                    <div class="profile-fact"><span>Member since</span><strong>{{ $user->created_at?->format('M Y') ?? '—' }}</strong></div>
                    <div class="profile-fact"><span>Completed fields</span><strong>{{ $completedFields }} of {{ $profileValues->count() }}</strong></div>
                </div>

                <a class="verification-link" href="{{ route('profile.verification.edit') }}">Manage account verification &rarr;</a>
            </div>
        </aside>

        <section class="panel profile-form-panel">
            <div class="panel-header"><div><h2 class="panel-title">Profile details</h2><p class="panel-description">Used for your loan applications, account notices, and payment communication.</p></div><span class="required-note">All fields are required</span></div>
            <form class="profile-form" id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" data-address-selects data-selected-city="{{ old('city_municipality', $cityMunicipality) }}" data-selected-barangay="{{ old('barangay', $barangay) }}">
                @csrf
                @method('patch')

                <div class="profile-form-section">
                    <div class="form-section-head"><span class="form-section-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 20c.5-4.5 2.8-7 7-7s6.5 2.5 7 7"/></svg></span><div><h3 class="form-section-title">Identity</h3><p class="form-section-note">Enter your name exactly as it appears on your valid identification.</p></div></div>
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label" for="first-name">First name</label><input class="form-control" id="first-name" name="first_name" value="{{ old('first_name', $user->first_name) }}" autocomplete="given-name" maxlength="100" required aria-invalid="{{ $errors->has('first_name') ? 'true' : 'false' }}">@error('first_name')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div class="form-group"><label class="form-label" for="last-name">Last name</label><input class="form-control" id="last-name" name="last_name" value="{{ old('last_name', $user->last_name) }}" autocomplete="family-name" maxlength="100" required aria-invalid="{{ $errors->has('last_name') ? 'true' : 'false' }}">@error('last_name')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div class="form-group"><label class="form-label" for="age">Age</label><input class="form-control" type="number" id="age" name="age" min="18" max="120" value="{{ old('age', $user->age) }}" inputmode="numeric" required aria-invalid="{{ $errors->has('age') ? 'true' : 'false' }}">@error('age')<p class="field-error">{{ $message }}</p>@enderror</div>
                    </div>
                </div>

                <div class="profile-form-section">
                    <div class="form-section-head"><span class="form-section-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16v12H4zM4 7l8 6 8-6"/></svg></span><div><h3 class="form-section-title">Contact information</h3><p class="form-section-note">We use these details for important account and repayment notices.</p></div></div>
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label" for="email">Email address</label><input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"><p class="form-help">Changing your email requires verification again.</p>@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div class="form-group"><label class="form-label" for="contact-number">Contact number</label><input class="form-control" type="tel" id="contact-number" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" autocomplete="tel" maxlength="30" placeholder="09XX XXX XXXX" required aria-invalid="{{ $errors->has('contact_number') ? 'true' : 'false' }}">@error('contact_number')<p class="field-error">{{ $message }}</p>@enderror</div>
                    </div>
                </div>

                <div class="profile-form-section">
                    <div class="form-section-head"><span class="form-section-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11z"/><circle cx="12" cy="10" r="2" stroke-width="1.8"/></svg></span><div><h3 class="form-section-title">Residential address</h3><p class="form-section-note">Uses the same street and Philippine location structure as account creation.</p></div></div>
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label" for="street-address">Street / building no.</label><input class="form-control" id="street-address" name="street_address" value="{{ old('street_address', $streetAddress) }}" placeholder="House no., building, or street" maxlength="255" autocomplete="address-line1" required aria-invalid="{{ $errors->has('street_address') ? 'true' : 'false' }}">@error('street_address')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div class="form-group"><label class="form-label" for="province">Province</label><select class="form-control" id="province" name="province" autocomplete="address-level1" required aria-invalid="{{ $errors->has('province') ? 'true' : 'false' }}"><option value="">Select province</option>@foreach(config('philippine_locations') as $location)<option value="{{ $location }}" @selected(old('province', $province) === $location)>{{ $location }}</option>@endforeach</select>@error('province')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div class="form-group"><label class="form-label" for="city-municipality">City / municipality</label><select class="form-control" id="city-municipality" name="city_municipality" autocomplete="address-level2" required disabled aria-invalid="{{ $errors->has('city_municipality') ? 'true' : 'false' }}"><option value="">Select province first</option></select>@error('city_municipality')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div class="form-group"><label class="form-label" for="barangay">Barangay</label><select class="form-control" id="barangay" name="barangay" autocomplete="address-level3" required disabled aria-invalid="{{ $errors->has('barangay') ? 'true' : 'false' }}"><option value="">Select city or municipality first</option></select><p class="form-help" id="address-lookup-status" aria-live="polite">Choose your province to load its cities and municipalities.</p>@error('barangay')<p class="field-error">{{ $message }}</p>@enderror</div>
                    </div>
                </div>

                <div class="form-actions"><span class="save-state" id="profile-save-state">All changes saved</span><button class="save-button" type="submit"><span>Save changes</span></button></div>
            </form>
        </section>
    </div>
</div></main>
<script>
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
        addressStatus.textContent = 'Select the barangay for the current residential address.';
    } catch (error) {
        barangaySelect.innerHTML = '<option value="">Unable to load barangays</option>';
        addressStatus.textContent = 'Location service is unavailable. Select the city again to retry.';
    }
};

const loadCities = async (provinceValue, cityValue = '', barangayValue = '') => {
    citySelect.disabled = true;
    barangaySelect.disabled = true;
    citySelect.innerHTML = '<option value="">Loading cities...</option>';
    barangaySelect.innerHTML = '<option value="">Select city or municipality first</option>';
    addressStatus.textContent = 'Loading cities and municipalities...';

    const endpoint = provinceValue === 'Metro Manila'
        ? `${psgcBaseUrl}/regions/1300000000/cities-municipalities`
        : `${psgcBaseUrl}/provinces/${encodeURIComponent(provinceValue)}/cities-municipalities`;

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

const profileForm = document.getElementById('profile-form');
const profileSaveState = document.getElementById('profile-save-state');
const profilePhotoInput = document.getElementById('profile-photo');
const profilePhotoPreview = document.getElementById('profile-photo-preview');
const profilePhotoInitials = document.getElementById('profile-photo-initials');
const profilePhotoHelp = document.getElementById('profile-photo-help');
let profilePhotoObjectUrl;

profilePhotoInput?.addEventListener('change', () => {
    const file = profilePhotoInput.files?.[0];
    if (!file) return;

    if (profilePhotoObjectUrl) URL.revokeObjectURL(profilePhotoObjectUrl);
    profilePhotoObjectUrl = URL.createObjectURL(file);
    profilePhotoPreview.src = profilePhotoObjectUrl;
    profilePhotoPreview.hidden = false;
    profilePhotoInitials.hidden = true;
    profilePhotoHelp.querySelector('strong').textContent = file.name;
    profileSaveState.textContent = 'Profile picture ready to save';
    profileSaveState.classList.add('is-dirty');
});

profileForm?.addEventListener('input', () => {
    profileSaveState.textContent = 'Unsaved changes';
    profileSaveState.classList.add('is-dirty');
});
profileForm?.addEventListener('submit', () => {
    const label = profileForm.querySelector('.save-button span');
    if (label) label.textContent = 'Saving changes';
});
</script>
</body></html>
