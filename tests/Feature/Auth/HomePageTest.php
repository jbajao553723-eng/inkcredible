<?php

it('uses the login screen as the landing page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Welcome back')
        ->assertSee('Sign in');
});
