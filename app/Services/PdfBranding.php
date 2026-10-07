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

        $path = public_path('images/inkcredible-logo.png');

        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        return $this->logoDataUri = 'data:image/png;base64,'.base64_encode($contents);
    }
}
