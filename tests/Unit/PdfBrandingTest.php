<?php

namespace Tests\Unit;

use App\Services\PdfBranding;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PdfBrandingTest extends TestCase
{
    #[Test]
    public function pdf_branding_uses_a_vector_logo_that_does_not_require_gd(): void
    {
        $dataUri = app(PdfBranding::class)->logoDataUri();

        $this->assertNotNull($dataUri);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $dataUri);

        $svg = base64_decode(substr($dataUri, strlen('data:image/svg+xml;base64,')), true);

        $this->assertIsString($svg);
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('#b91c1c', $svg);
        $this->assertStringNotContainsString('<image', $svg);
    }
}
