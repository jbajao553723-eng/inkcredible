<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms and Conditions | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>@include('partials.legal-styles')</style>
</head>
<body>
<header class="legal-header"><div class="header-inner"><a class="brand" href="{{ route('login') }}"><span class="brand-mark">I</span>Inkcredible</a><div class="eyebrow">Account agreement</div><h1>Terms and Conditions</h1><p class="header-description">The rules for creating and using an Inkcredible account and accessing its lending-management services.</p></div></header>
<main class="legal-main"><article class="legal-card">
    <div class="legal-meta"><span>Version {{ config('legal.account_terms_version') }}</span><span>Effective September 14, 2026</span></div>
    <div class="legal-notice"><strong>Local project notice:</strong> This is a practical terms template for the localhost application. Before operating a real lending business, the operator should have these terms, rates, licenses, contact details, and privacy practices reviewed by qualified Philippine counsel.</div>

    <section class="section"><h2>1. Account eligibility and truthful information</h2><p>You must provide accurate, current, and complete registration and identity information. You are responsible for correcting information that changes and for keeping your password confidential. You may not create an account using another person's identity or submit altered, forged, or misleading records.</p></section>
    <section class="section"><h2>2. Permitted use</h2><p>The account may be used to submit loan applications, review decisions and balances, upload required documents, and make or record payments. You must not attempt to interfere with the service, bypass security controls, misuse another account, or use the platform for unlawful activity.</p></section>
    <section class="section"><h2>3. Personal information</h2><p>Information may be processed for account administration, identity verification, evaluation of loan requests, servicing and collection of approved loans, payment processing, fraud prevention, support, recordkeeping, and compliance with lawful requirements. Collection and use should remain necessary and proportionate to these purposes.</p><p>Payment information may be sent to the configured payment provider to complete and verify online transactions. Personal information must not be used for harassment, public shaming, or indiscriminate contact-list collection.</p></section>
    <section class="section"><h2>4. Account security and availability</h2><p>You must promptly report suspected unauthorized access. The service may restrict access to protect accounts, investigate misuse, perform maintenance, or comply with law. Temporary interruptions do not cancel an existing valid repayment obligation.</p></section>
    <section class="section"><h2>5. Loan-specific terms</h2><p>Creating an account does not guarantee approval. Every loan application requires separate acceptance of the Loan Terms and Borrower Disclosure, including the applicable principal, interest, estimated total repayment, due date, payment options, and consequences of default.</p></section>
    <section class="section"><h2>6. Fraud and unlawful conduct</h2><p>Suspected identity theft, forged documentation, deliberate payment fraud, unauthorized access, or other potentially unlawful conduct may result in account restriction and may be preserved and reported to appropriate law-enforcement agencies, regulators, payment providers, or other authorities when permitted or required by law.</p><div class="highlight">Ordinary inability or failure to repay is not automatically described as a criminal offense. Debt collection must use reasonable and legally permissible methods.</div></section>
    <section class="section"><h2>7. Changes and acceptance records</h2><p>The application may present updated terms when material provisions change. The accepted version and date are recorded. Continuing to use features that require new consent may require acceptance of the updated version.</p></section>

    <div class="legal-actions"><a class="button" href="{{ route('login') }}">Back to sign in</a><a class="button button-primary" href="{{ route('register') }}">Create account</a></div>
</article></main>
</body></html>
