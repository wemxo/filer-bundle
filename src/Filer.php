<?php

declare(strict_types=1);

namespace Wemxo\FilerBundle;

use Gaufrette\File;
use Gaufrette\FilesystemInterface;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\Mime\MimeTypes;

class Filer implements FilerInterface
{
    public const WATERMARK_FILTER = 'watermark';

    private array $typesConfiguration = [];

    private array $filesystems = [];

    public function __construct(private FilterManager $filterManager)
    {
    }

    public function addTypeConfiguration(string $type, array $configuration): void
    {
        $this->typesConfiguration[$type] = $configuration;
    }

    public function addFileSystem(string $access, FilesystemInterface $filesystem): void
    {
        $this->filesystems[$access] = $filesystem;
    }

    public function saveFile(FilerInput $input): FilerOutput
    {
        $typeConfiguration = $this->getTypeConfiguration($input->getType());
        $this->validateInput($input, $typeConfiguration);
        if (!$this->isImage($input->getMimeType())) {
            $file = $this->createFileFromInput($input, $typeConfiguration);

            return new FilerOutput(
                $input->getOriginalFilename(),
                $input->getMimeType(),
                $typeConfiguration->access,
                $file->getKey(),
                $input->getType(),
                $file->getSize()
            );
        }

        $binary = $this->applyWaterMark($input->getContent(), $input->getMimeType(), $typeConfiguration);
        $file = $this->createFile($binary->getContent(), $typeConfiguration->folder, $typeConfiguration->access, $input->getMimeType());
        $resizedFiles = $this->applyFilters($input, $typeConfiguration);
        if (!$typeConfiguration->keepSource) {
            $this->removeFile($typeConfiguration->type, $file->getKey());
            foreach ($resizedFiles as $index => $resizedFile) {
                if ($resizedFile->filter !== $typeConfiguration->source) {
                    continue;
                }

                $file = new File($resizedFile->fullName, $this->getFileSystem($typeConfiguration->access));
                unset($resizedFiles[$index]);

                break;
            }
        }

        return new FilerOutput(
            $input->getOriginalFilename(),
            $input->getMimeType(),
            $typeConfiguration->access,
            $file->getKey(),
            $input->getType(),
            $file->getSize(),
            $resizedFiles
        );
    }

    public function removeFile(string $type, string $key): void
    {
        $configuration = $this->getTypeConfiguration($type);
        $fileSystem = $this->getFileSystem($configuration->access);
        if (!$fileSystem->has($key)) {
            return;
        }

        try {
            $fileSystem->delete($key);
        } catch (\Throwable $throwable) {
        }
    }

    public function getFile(string $type, string $key): File
    {
        $configuration = $this->getTypeConfiguration($type);
        $fileSystem = $this->getFileSystem($configuration->access);
        if (!$fileSystem->has($key)) {
            throw new FilerException('File not found !');
        }

        return $fileSystem->get($key);
    }

    /**
     * @throws FilerException
     */
    protected function createFileFromInput(FilerInput $input, TypeConfiguration $configuration): File
    {
        return $this->createFile($input->getContent(), $configuration->folder, $configuration->access, $input->getMimeType());
    }

    /**
     * @return ResizedFile[]
     *
     * @throws FilerException
     */
    protected function applyFilters(FilerInput $input, TypeConfiguration $typeConfiguration): array
    {
        if (empty($typeConfiguration->filters)) {
            return [];
        }

        $binary = new Binary($input->getContent(), $input->getMimeType(), explode('/', $input->getMimeType())[1]);
        $resizedFiles = [];
        foreach ($typeConfiguration->filters as $filter) {
            $filteredBinary = $this->filterManager->applyFilter(
                $binary,
                $filter
            );
            $filteredBinary = $this->applyWaterMark($input->getContent(), $input->getMimeType(), $typeConfiguration, $filteredBinary);
            $file = $this->createFile($filteredBinary->getContent(), $typeConfiguration->folder, $typeConfiguration->access, $input->getMimeType());
            $resizedFiles[] = new ResizedFile($filter, $file->getKey(), $file->getSize());
        }

        return $resizedFiles;
    }

    protected function applyWaterMark(string $content, string $mimetype, TypeConfiguration $typeConfiguration, ?BinaryInterface $binary = null): BinaryInterface
    {
        if (!$binary) {
            $binary = new Binary($content, $mimetype, explode('/', $mimetype)[1]);
        }

        if (!$this->isWatermarkEnabled() || !$typeConfiguration->applyWatermark) {
            return $binary;
        }

        return $this->filterManager->applyFilter($binary, self::WATERMARK_FILTER);
    }

    /**
     * @throws FilerException
     */
    protected function getFileSystem(string $access): FilesystemInterface
    {
        if (!array_key_exists($access, $this->filesystems)) {
            throw new FilerException('Unable to find filesystem for the given access !');
        }

        return $this->filesystems[$access];
    }

    protected function generateFullName(string $folder, string $mimeType): string
    {
        $extension = (new MimeTypes())->getExtensions($mimeType)[0] ?? null;
        $uuid = md5(uniqid().time());
        $uuid = $extension ? sprintf('%s.%s', $uuid, $extension) : $uuid;
        $subFolders = sprintf(
            '%s/%s/%s',
            substr($uuid, 0, 2),
            substr($uuid, 2, 2),
            substr($uuid, 4, 2)
        );

        return sprintf('%s/%s/%s', $folder, $subFolders, $uuid);
    }

    /**
     * @throws FilerException
     */
    protected function validateInputAppliedFilters(array $appliedFilter): void
    {
        if (empty($appliedFilter)) {
            return;
        }

        $configuredFilters = $this->filterManager->getFilterConfiguration()->all();
        $unConfiguredFilters = array_diff($appliedFilter, array_keys($configuredFilters));
        if (!empty($unConfiguredFilters)) {
            throw new FilerException('Invalid filters given !');
        }
    }

    /**
     * @throws FilerException
     */
    protected function createFile(string $content, string $folder, string $access, string $mimeType): File
    {
        $fullName = $this->generateFullName($folder, $mimeType);
        $file = new File($fullName, $this->getFileSystem($access));
        $file->setContent($content);

        return $file;
    }

    protected function isWatermarkEnabled(): bool
    {
        return in_array(
            self::WATERMARK_FILTER,
            array_keys($this->filterManager->getFilterConfiguration()->all())
        );
    }

    protected function isImage(string $mimeType): bool
    {
        return str_contains($mimeType, 'image/');
    }

    /**
     * @throws FilerValidationException
     */
    private function validateInput(FilerInput $input, TypeConfiguration $typeConfiguration): void
    {
        if (!in_array($input->getMimeType(), $typeConfiguration->mimeTypes)) {
            throw new FilerValidationException('Invalid input given', [sprintf('Invalid file type given (%s), allowed file types are [%s]', $input->getMimeType(), implode(', ', $typeConfiguration->mimeTypes))]);
        }

        if ($typeConfiguration->maxSize < $input->getSize()) {
            throw new FilerValidationException('Invalid input given', [sprintf('Max size exceeded %d Bytes, allowed max size is %d Bytes', $input->getSize(), $typeConfiguration->maxSize)]);
        }
    }

    /**
     * @throws FilerValidationException
     */
    private function getTypeConfiguration(string $type): TypeConfiguration
    {
        if (!array_key_exists($type, $this->typesConfiguration)) {
            throw new FilerValidationException('Invalid type given !');
        }

        $configuration = $this->typesConfiguration[$type];

        return new TypeConfiguration(
            $type,
            $configuration['folder'],
            $configuration['mimeTypes'],
            $configuration['maxSize'],
            $configuration['access'],
            $configuration['filters'],
            $configuration['applyWatermark'],
            $configuration['keepSource'],
            $configuration['source']
        );
    }
}
