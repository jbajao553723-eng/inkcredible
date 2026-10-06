<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DigitalSignature implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! str_starts_with($value, 'data:image/png;base64,')) {
            $fail('Please draw a valid digital signature.');

            return;
        }

        $imageData = base64_decode(substr($value, strlen('data:image/png;base64,')), true);

        if ($imageData === false || strlen($imageData) > 350_000) {
            $fail('The digital signature is invalid or too large.');

            return;
        }

        $imageInfo = @getimagesizefromstring($imageData);

        if ($imageInfo === false || ($imageInfo['mime'] ?? null) !== 'image/png') {
            $fail('The digital signature must be a valid PNG image.');

            return;
        }

        [$width, $height] = $imageInfo;

        if ($width < 200 || $height < 80 || $width > 2_000 || $height > 1_000) {
            $fail('The digital signature has invalid dimensions.');

            return;
        }

        // The Vercel PHP runtime does not provide GD. The browser signature
        // pad already rejects an empty canvas, while the checks above still
        // enforce a genuine, bounded PNG on runtimes without that extension.
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagecolorat')) {
            return;
        }

        $image = @imagecreatefromstring($imageData);

        if ($image === false) {
            $fail('The digital signature must be a valid PNG image.');

            return;
        }

        $inkPixels = 0;
        $step = max(1, (int) floor(min($width, $height) / 100));

        for ($y = 0; $y < $height && $inkPixels < 20; $y += $step) {
            for ($x = 0; $x < $width && $inkPixels < 20; $x += $step) {
                $color = imagecolorat($image, $x, $y);
                $red = ($color >> 16) & 0xFF;
                $green = ($color >> 8) & 0xFF;
                $blue = $color & 0xFF;

                if ($red < 230 || $green < 230 || $blue < 230) {
                    $inkPixels++;
                }
            }
        }

        imagedestroy($image);

        if ($inkPixels < 20) {
            $fail('Please draw your signature before continuing.');
        }
    }
}
