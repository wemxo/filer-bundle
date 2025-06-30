<?php

declare(strict_types=1);

namespace Wemxo\FilerBundle;

class FilerOutput
{
    public function __construct(
        private string $originalFileName,
        private string $mimetype,
        private string $access,
        private string $fullName,
        private string $type,
        private int $size,
        private array $resideFiles = [],
    ) {
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    public function getAccess(): string
    {
        return $this->access;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return ResizedFile[]
     */
    public function getResideFiles(): array
    {
        return $this->resideFiles;
    }
}
