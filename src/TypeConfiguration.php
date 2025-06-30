<?php

declare(strict_types=1);

namespace Wemxo\FilerBundle;

class TypeConfiguration
{
    public function __construct(
        public string $type,
        public string $folder,
        public array $mimeTypes,
        public int $maxSize,
        public string $access,
        public array $filters,
        public bool $applyWatermark,
        public bool $keepSource,
        public ?string $source,
    ) {
    }
}
