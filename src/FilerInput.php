<?php

declare(strict_types=1);

namespace Wemxo\FilerBundle;

class FilerInput
{
    public function __construct(
        private string $originalFilename,
        private string $content,
        private string $mimeType,
        private int $size,
        private string $type,
    ) {
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
