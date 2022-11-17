<?php

namespace Core\Http;

use RuntimeException;
use SplFileInfo;

class UploadedFile extends SplFileInfo
{
    /**
     * @var int The size, in bytes, of the uploaded file.
     */
    private int $size;

    /**
     * @var int The error code associated with this file upload.
     */
    private int $error;

    /**
     * @var string|null The file name of the uploaded file.
     */
    private ?string $clientFilename;

    /**
     * @var string|null The media type of the uploaded file.
     */
    private ?string $clientMediaType;

    /**
     * @var bool Whether the file has already been moved.
     */
    private bool $moved = false;

    /**
     * Map of mime types to extensions.
     */
    private const mimeMap = [
        'image/bmp' => ['bmp', 'dib'],
        'image/gif' => ['gif'],
        'image/ico' => ['ico'],
        'image/icon' => ['ico'],
        'image/jpeg' => ['jpg', 'jpeg', 'jpe'],
        'image/jpeg2000' => ['jp2', 'jpg2'],
        'image/jpeg2000-image' => ['jp2', 'jpg2'],
        'image/png' => ['png'],
        'image/svg' => ['svg'],
        'image/webp' => ['webp'],
    ];

    /**
     * Constructor.
     *
     * @param string $tmpName
     * @param int $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    public function __construct(string $tmpName, int $size, int $error, ?string $clientFilename = null, ?string $clientMediaType = null)
    {
        parent::__construct($tmpName);
        $this->size = $size;
        $this->error = $error;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    /**
     * @return string|null
     */
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }

    /**
     * @return bool
     */
    public function isMoved(): bool
    {
        return $this->moved;
    }

    /**
     * Move the uploaded file to a new location.
     *
     * @param string $targetPath
     * @return void
     */
    public function moveTo(string $targetPath): void
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Cannot move the file due to upload error');
        }

        if ($this->isMoved()) {
            throw new RuntimeException('The file has already been moved');
        }

        if (!is_writable(dirname($targetPath))) {
            throw new RuntimeException('Upload target path is not writable');
        }

        if (!move_uploaded_file($this->getPathname(), $targetPath)) {
            throw new RuntimeException('Uploaded file could not be moved to target path');
        }

        $this->moved = true;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK && is_uploaded_file($this->getPathname());
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->error === UPLOAD_ERR_NO_FILE;
    }

    /**
     * Store the uploaded file in the given subdirectory of the storage folder.
     * If name is not specified, a unique name will be generated.
     *
     * @param string|null $path
     * @param string|null $name
     * @return string The name of the stored file.
     */
    public function store(string $path = null, string $name = null): string
    {
        $name = $name ?? uniqid('', true) . '.' . ($this->guessExtension() ?? 'tmp') ;
        $localPath = rtrim(app()->config('storage_dir'), '/') .
            DIRECTORY_SEPARATOR . rtrim($path, '/') .
            DIRECTORY_SEPARATOR . $name;
        $fullPath = rtrim(app()->config('root_dir'), '/') .
            DIRECTORY_SEPARATOR . $localPath;
        $this->moveTo($fullPath);
        return $localPath;
    }

    /**
     * Try to get a file extension based on the mime type of this file.
     *
     * @return string|null
     */
    public function guessExtension(): ?string
    {
        $type = mime_content_type($this->getPathname());
        if (isset(self::mimeMap[$type])) {
            return self::mimeMap[$type][0];
        }
        return null;
    }

    /**
     * Delete a file.
     *
     * @param string $path The path to the file relative to the root directory.
     * @return void
     */
    public static function delete(string $path): void
    {
        $fullPath = rtrim(app()->config('root_dir'), '/') . DIRECTORY_SEPARATOR . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}