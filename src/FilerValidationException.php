<?php

declare(strict_types=1);

namespace Wemxo\FilerBundle;

class FilerValidationException extends \Exception
{
    public function __construct(string $message = '', private array $errors = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
