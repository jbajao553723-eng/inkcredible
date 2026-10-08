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
        // not provide GD, so PDF documents use this small vector brand mark.
        // SVG remains sharp in print and can be embedded without temporary
        // files or image-processing extensions.
        $svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <rect width="64" height="64" rx="14" fill="#b91c1c"/>
  <circle cx="32" cy="32" r="21" fill="#ffffff"/>
  <path fill="#b91c1c" d="M22 19h20v6h-6v14h6v6H22v-6h6V25h-6z"/>
</svg>
SVG;

        return $this->logoDataUri = 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
