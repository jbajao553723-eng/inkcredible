<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentExperienceTest extends TestCase
{
    #[Test]
    public function paymongo_checkout_uses_the_current_tab_and_returns_to_inkcredible_routes(): void
    {
        $paymentPage = file_get_contents(resource_path('views/payments/index.blade.php'));
        $successPage = file_get_contents(resource_path('views/payments/success.blade.php'));
        $cancelPage = file_get_contents(resource_path('views/payments/cancel.blade.php'));

        $this->assertIsString($paymentPage);
        $this->assertStringContainsString('window.location.assign(result.checkout_url)', $paymentPage);
        $this->assertStringNotContainsString('window.open(', $paymentPage);
        $this->assertStringNotContainsString('checkoutWindow', $paymentPage);
        $this->assertStringNotContainsString('window.opener', $successPage);
        $this->assertStringNotContainsString('window.opener', $cancelPage);
        $this->assertStringContainsString('Download PDF receipt', $successPage);
    }
}
