<?php

namespace Tests\Unit;

use App\Services\PdfBranding;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PdfBrandingTest extends TestCase
{
    #[Test]
    public function pdf_branding_uses_the_supplied_jpeg_logo_without_requiring_gd(): void
    {
        $dataUri = app(PdfBranding::class)->logoDataUri();

        $this->assertNotNull($dataUri);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $dataUri);

        $jpeg = base64_decode(substr($dataUri, strlen('data:image/jpeg;base64,')), true);

        $this->assertIsString($jpeg);
        $this->assertStringStartsWith("\xFF\xD8\xFF", $jpeg);
        $this->assertSame(
            hash_file('sha256', public_path('images/inkcredible-pdf-logo.jpg')),
            hash('sha256', $jpeg),
        );
    }
}
