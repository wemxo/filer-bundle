<?php

declare(strict_types=1);

namespace Wemxo\FilerBundle;

class ResizedFile
{
    public function __construct(public string $filter, public string $fullName, public int $size)
    {
    }
}
