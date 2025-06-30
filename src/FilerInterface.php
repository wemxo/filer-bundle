<?php

declare(strict_types=1);

namespace Wemxo\FilerBundle;

use Gaufrette\File;
use Gaufrette\FilesystemInterface;

interface FilerInterface
{
    public function addTypeConfiguration(string $type, array $configuration): void;

    public function addFileSystem(string $access, FilesystemInterface $filesystem): void;

    /**
     * @throws FilerException
     * @throws FilerValidationException
     */
    public function saveFile(FilerInput $input): FilerOutput;

    /**
     * @throws FilerException
     * @throws FilerValidationException
     */
    public function removeFile(string $type, string $key): void;

    /**
     * @throws FilerException
     * @throws FilerValidationException
     */
    public function getFile(string $type, string $key): File;
}
