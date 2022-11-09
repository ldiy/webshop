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

}