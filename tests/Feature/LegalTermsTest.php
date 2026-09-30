<?php

it('publishes account and loan terms before registration', function () {
    $this->get(route('terms'))
        ->assertOk()
        ->assertSee('Terms and Conditions')
        ->assertSee('Fraud and unlawful conduct')
        ->assertSee(config('legal.account_terms_version'));

    $this->get(route('loan.terms'))
        ->assertOk()
        ->assertSee('Loan Terms and Conditions')
        ->assertSee('Penalties and charges')
        ->assertSee('Reports to authorities')
        ->assertSee('PHP 15,000')
        ->assertSee('PHP 50,000')
        ->assertSee(config('legal.loan_terms_version'));
});
