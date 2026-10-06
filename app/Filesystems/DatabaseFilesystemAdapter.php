<?php

namespace App\Filesystems;

use App\Models\StoredFile;
use Illuminate\Database\Eloquent\Builder;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use RuntimeException;
use Throwable;

class DatabaseFilesystemAdapter implements FilesystemAdapter
{
    public function __construct(private readonly string $disk) {}

    public function fileExists(string $path): bool
    {
        return $this->query($path)->exists();
    }

    public function directoryExists(string $path): bool
    {
        $prefix = $this->directoryPrefix($path);

        return $this->diskQuery()->where('path', 'like', $prefix.'%')->exists();
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $path = $this->normalizePath($path);

        try {
            StoredFile::query()->updateOrCreate(
                ['disk' => $this->disk, 'path' => $path],
                [
                    'contents_base64' => base64_encode($contents),
                    'mime_type' => $config->get('mimetype') ?: $this->detectMimeType($contents),
                    'size' => strlen($contents),
                    'visibility' => $config->get('visibility', Visibility::PRIVATE),
                ],
            );
        } catch (Throwable $exception) {
            throw UnableToWriteFile::atLocation($path, $exception->getMessage(), $exception);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        if (! is_resource($contents)) {
            throw UnableToWriteFile::atLocation($path, 'The supplied contents are not a stream.');
        }

        $data = stream_get_contents($contents);

        if ($data === false) {
            throw UnableToWriteFile::atLocation($path, 'The stream could not be read.');
        }

        $this->write($path, $data, $config);
    }

    public function read(string $path): string
    {
        $path = $this->normalizePath($path);
        $file = $this->query($path)->first();

        if (! $file) {
            throw UnableToReadFile::fromLocation($path, 'File does not exist.');
        }

        $contents = base64_decode($file->contents_base64, true);

        if ($contents === false) {
            throw UnableToReadFile::fromLocation($path, 'Stored file contents are invalid.');
        }

        return $contents;
    }

    public function readStream(string $path)
    {
        $stream = fopen('php://temp', 'w+b');

        if ($stream === false || fwrite($stream, $this->read($path)) === false) {
            throw UnableToReadFile::fromLocation($path, 'Unable to create a readable stream.');
        }

        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $this->query($path)->delete();
    }

    public function deleteDirectory(string $path): void
    {
        $path = $this->normalizePath($path, allowEmpty: true);

        if ($path === '') {
            $this->diskQuery()->delete();

            return;
        }

        $this->diskQuery()
            ->where(fn (Builder $query) => $query
                ->where('path', $path)
                ->orWhere('path', 'like', $path.'/%'))
            ->delete();
    }

    public function createDirectory(string $path, Config $config): void
    {
        // Directories are virtual and inferred from stored file paths.
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $path = $this->normalizePath($path);

        if (! in_array($visibility, [Visibility::PUBLIC, Visibility::PRIVATE], true)) {
            throw UnableToSetVisibility::atLocation($path, 'Invalid visibility value.');
        }

        if ($this->query($path)->update(['visibility' => $visibility]) === 0) {
            throw UnableToSetVisibility::atLocation($path, 'File does not exist.');
        }
    }

    public function visibility(string $path): FileAttributes
    {
        return $this->attributes($path, 'visibility');
    }

    public function mimeType(string $path): FileAttributes
    {
        return $this->attributes($path, 'mimeType');
    }

    public function lastModified(string $path): FileAttributes
    {
        return $this->attributes($path, 'lastModified');
    }

    public function fileSize(string $path): FileAttributes
    {
        return $this->attributes($path, 'fileSize');
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $path = $this->normalizePath($path, allowEmpty: true);
        $query = $this->diskQuery()->orderBy('path');

        if ($path !== '') {
            $query->where('path', 'like', $this->directoryPrefix($path).'%');
        }

        foreach ($query->cursor() as $file) {
            $relativePath = $path === '' ? $file->path : substr($file->path, strlen($path) + 1);

            if (! $deep && str_contains($relativePath, '/')) {
                continue;
            }

            yield $this->fileAttributes($file);
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        if ($this->normalizePath($source) === $this->normalizePath($destination)) {
            throw UnableToMoveFile::sourceAndDestinationAreTheSame($source, $destination);
        }

        try {
            $this->copy($source, $destination, $config);
            $this->delete($source);
        } catch (Throwable $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $exception);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        if ($this->normalizePath($source) === $this->normalizePath($destination)) {
            throw UnableToCopyFile::sourceAndDestinationAreTheSame($source, $destination);
        }

        try {
            $sourceFile = $this->query($source)->firstOrFail();
            $config = new Config([
                'mimetype' => $sourceFile->mime_type,
                'visibility' => $config->get('visibility', $sourceFile->visibility),
            ]);
            $this->write($destination, $this->read($source), $config);
        } catch (Throwable $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    private function attributes(string $path, string $type): FileAttributes
    {
        $path = $this->normalizePath($path);
        $file = $this->query($path)->first();

        if (! $file) {
            throw match ($type) {
                'visibility' => UnableToRetrieveMetadata::visibility($path, 'File does not exist.'),
                'mimeType' => UnableToRetrieveMetadata::mimeType($path, 'File does not exist.'),
                'lastModified' => UnableToRetrieveMetadata::lastModified($path, 'File does not exist.'),
                default => UnableToRetrieveMetadata::fileSize($path, 'File does not exist.'),
            };
        }

        return $this->fileAttributes($file);
    }

    private function fileAttributes(StoredFile $file): FileAttributes
    {
        return new FileAttributes(
            $file->path,
            $file->size,
            $file->visibility,
            $file->updated_at?->getTimestamp(),
            $file->mime_type,
        );
    }

    private function diskQuery(): Builder
    {
        return StoredFile::query()->where('disk', $this->disk);
    }

    private function query(string $path): Builder
    {
        return $this->diskQuery()->where('path', $this->normalizePath($path));
    }

    private function directoryPrefix(string $path): string
    {
        $path = $this->normalizePath($path, allowEmpty: true);

        return $path === '' ? '' : $path.'/';
    }

    private function normalizePath(string $path, bool $allowEmpty = false): string
    {
        $path = trim(str_replace('\\', '/', $path), '/');

        if (! $allowEmpty && $path === '') {
            throw new RuntimeException('A stored file path cannot be empty.');
        }

        if (str_contains('/'.$path.'/', '/../') || str_contains('/'.$path.'/', '/./')) {
            throw new RuntimeException('Relative path segments are not allowed.');
        }

        return $path;
    }

    private function detectMimeType(string $contents): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        return $finfo->buffer($contents) ?: 'application/octet-stream';
    }
}
