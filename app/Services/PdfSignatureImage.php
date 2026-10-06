<?php

namespace App\Services;

class PdfSignatureImage
{
    public function prepare(?string $dataUrl): ?string
    {
        if (! is_string($dataUrl) || $dataUrl === '') {
            return null;
        }

        if (preg_match('#^data:image/(?:jpeg|jpg);base64,[A-Za-z0-9+/=]+$#', $dataUrl) === 1) {
            return $dataUrl;
        }

        if (! str_starts_with($dataUrl, 'data:image/png;base64,')) {
            return null;
        }

        $png = base64_decode(substr($dataUrl, strlen('data:image/png;base64,')), true);

        if ($png === false) {
            return null;
        }

        $svg = $this->pngToSvg($png);

        return $svg === null
            ? null
            : 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    private function pngToSvg(string $png): ?string
    {
        if (! str_starts_with($png, "\x89PNG\r\n\x1a\n")) {
            return null;
        }

        $offset = 8;
        $width = $height = $bitDepth = $colorType = $interlace = null;
        $compressed = '';

        while ($offset + 12 <= strlen($png)) {
            $length = unpack('N', substr($png, $offset, 4))[1];
            $type = substr($png, $offset + 4, 4);
            $data = substr($png, $offset + 8, $length);
            $offset += 12 + $length;

            if ($type === 'IHDR' && strlen($data) === 13) {
                $header = unpack('Nwidth/Nheight/CbitDepth/CcolorType/Ccompression/Cfilter/Cinterlace', $data);
                $width = $header['width'];
                $height = $header['height'];
                $bitDepth = $header['bitDepth'];
                $colorType = $header['colorType'];
                $interlace = $header['interlace'];
            } elseif ($type === 'IDAT') {
                $compressed .= $data;
            } elseif ($type === 'IEND') {
                break;
            }
        }

        $channels = match ($colorType) {
            0 => 1,
            2 => 3,
            4 => 2,
            6 => 4,
            default => null,
        };

        if (! is_int($width) || ! is_int($height)
            || $width < 1 || $height < 1 || $width > 2_000 || $height > 1_000
            || $bitDepth !== 8 || $interlace !== 0 || $channels === null || $compressed === '') {
            return null;
        }

        $inflated = @zlib_decode($compressed);
        $stride = $width * $channels;

        if ($inflated === false || strlen($inflated) !== ($stride + 1) * $height) {
            return null;
        }

        $position = 0;
        $previous = array_fill(0, $stride, 0);
        $path = '';

        for ($y = 0; $y < $height; $y++) {
            $filter = ord($inflated[$position++]);
            $raw = array_values(unpack('C*', substr($inflated, $position, $stride)));
            $position += $stride;
            $current = [];

            for ($index = 0; $index < $stride; $index++) {
                $left = $index >= $channels ? $current[$index - $channels] : 0;
                $up = $previous[$index];
                $upperLeft = $index >= $channels ? $previous[$index - $channels] : 0;
                $predictor = match ($filter) {
                    0 => 0,
                    1 => $left,
                    2 => $up,
                    3 => intdiv($left + $up, 2),
                    4 => $this->paeth($left, $up, $upperLeft),
                    default => null,
                };

                if ($predictor === null) {
                    return null;
                }

                $current[$index] = ($raw[$index] + $predictor) & 0xFF;
            }

            $runStart = null;

            for ($x = 0; $x <= $width; $x++) {
                $ink = $x < $width && $this->isInk($current, $x * $channels, $colorType);

                if ($ink && $runStart === null) {
                    $runStart = $x;
                } elseif (! $ink && $runStart !== null) {
                    $runLength = $x - $runStart;
                    $path .= "M{$runStart} {$y}h{$runLength}v1h-{$runLength}z";
                    $runStart = null;
                }
            }

            $previous = $current;
        }

        if ($path === '') {
            return null;
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$width.' '.$height.'">'
            .'<path fill="#172033" d="'.$path.'"/></svg>';
    }

    /** @param array<int, int> $row */
    private function isInk(array $row, int $offset, int $colorType): bool
    {
        [$red, $green, $blue, $alpha] = match ($colorType) {
            0 => [$row[$offset], $row[$offset], $row[$offset], 255],
            2 => [$row[$offset], $row[$offset + 1], $row[$offset + 2], 255],
            4 => [$row[$offset], $row[$offset], $row[$offset], $row[$offset + 1]],
            6 => [$row[$offset], $row[$offset + 1], $row[$offset + 2], $row[$offset + 3]],
        };

        return $alpha >= 32 && min($red, $green, $blue) < 235;
    }

    private function paeth(int $left, int $up, int $upperLeft): int
    {
        $estimate = $left + $up - $upperLeft;
        $leftDistance = abs($estimate - $left);
        $upDistance = abs($estimate - $up);
        $upperLeftDistance = abs($estimate - $upperLeft);

        if ($leftDistance <= $upDistance && $leftDistance <= $upperLeftDistance) {
            return $left;
        }

        return $upDistance <= $upperLeftDistance ? $up : $upperLeft;
    }
}
