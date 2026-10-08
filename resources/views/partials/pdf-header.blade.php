<table class="document-header"><tr>
    <td class="logo-cell">@if(! empty($logoDataUri))<img class="document-logo" src="{{ $logoDataUri }}" alt="Inkcredible Lending">@endif</td>
    <td><div class="document-company">Inkcredible Lending</div><div class="document-address">CM Recto St., Davao City</div></td>
    <td class="document-meta"><strong>{{ $documentReference }}</strong><br>{{ $documentDate }} PHT<br>{{ $documentClassification ?? 'Account document' }}</td>
</tr></table>
<div class="document-kicker">{{ $documentCategory }}</div>
<h1 class="document-title">{{ $documentTitle }}</h1>
<p class="document-subtitle">{{ $documentSubtitle }}</p>
