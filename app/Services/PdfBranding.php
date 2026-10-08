<?php

namespace App\Services;

class PdfBranding
{
    private ?string $logoDataUri = null;

    public function logoDataUri(): ?string
    {
        if ($this->logoDataUri !== null) {
            return $this->logoDataUri;
        }

        // Dompdf needs PHP GD to decode PNG files. The Vercel PHP runtime does
        // not provide GD, so the supplied company logo is stored as JPEG and
        // embedded directly without image-processing extensions.
        $path = public_path('images/inkcredible-pdf-logo.jpg');

        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false || ! str_starts_with($contents, "\xFF\xD8\xFF")) {
            return null;
        }

        return $this->logoDataUri = 'data:image/jpeg;base64,'.base64_encode($contents);
    }
}
