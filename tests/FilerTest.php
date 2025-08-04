<?php

namespace Wemxo\FilerBundle\Tests;

use Gaufrette\Filesystem;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use PHPUnit\Framework\TestCase;
use Wemxo\FilerBundle\Filer;
use Wemxo\FilerBundle\FilerInput;
use Wemxo\FilerBundle\FilerValidationException;

class FilerTest extends TestCase
{
    public function testAddTypeConfiguration(): void
    {
        $filer = new Filer($this->createMock(FilterManager::class));
        $configuration = ['key' => 'value'];
        $filer->addTypeConfiguration('test_type', $configuration);
        $reflectionClass = new \ReflectionClass(get_class($filer));
        $reflectionProperty = $reflectionClass->getProperty('typesConfiguration');
        $reflectionProperty->setAccessible(true);
        $typesConfiguration = $reflectionProperty->getValue($filer);
        $this->assertArrayHasKey('test_type', $typesConfiguration);
        $testTypeConfiguration = $typesConfiguration['test_type'];
        $this->assertSame($configuration, $testTypeConfiguration);
    }

    public function testAddFileSystem(): void
    {
        $filer = new Filer($this->createMock(FilterManager::class));
        $fileSystem = $this->createMock(Filesystem::class);
        $filer->addFileSystem('test_access', $fileSystem);
        $reflectionClass = new \ReflectionClass(get_class($filer));
        $reflectionProperty = $reflectionClass->getProperty('filesystems');
        $reflectionProperty->setAccessible(true);
        $fileSystems = $reflectionProperty->getValue($filer);
        $this->assertArrayHasKey('test_access', $fileSystems);
        $testFileSystem = $fileSystems['test_access'];
        $this->assertSame($fileSystem, $testFileSystem);
    }

    public function testSaveFileTypeException(): void
    {
        $filerInput = $this->createMock(FilerInput::class);
        $filerInput
            ->expects($this->once())
            ->method('getType')
            ->willReturn('test_type')
        ;
        $filterManager = $this->createMock(FilterManager::class);
        $filer = new Filer($filterManager);
        $this->expectException(FilerValidationException::class);
        $this->expectExceptionMessage('Invalid type given !');
        $filer->saveFile($filerInput);
    }

    public function testSaveFileMimetypeException(): void
    {
        $filerInput = $this->createMock(FilerInput::class);
        $filerInput
            ->expects($this->once())
            ->method('getType')
            ->willReturn('test_type')
        ;
        $filerInput
            ->expects($this->exactly(2))
            ->method('getMimeType')
            ->willReturn('not/expected')
        ;
        $filterManager = $this->createMock(FilterManager::class);
        $filer = new Filer($filterManager);
        $filer->addTypeConfiguration('test_type', $this->getConfiguration());
        $this->expectException(FilerValidationException::class);
        $this->expectExceptionMessage('Invalid input given');
        $filer->saveFile($filerInput);
    }

    public function testSaveFileMaxSizeException(): void
    {
        $filerInput = $this->createMock(FilerInput::class);
        $filerInput
            ->expects($this->once())
            ->method('getType')
            ->willReturn('test_type')
        ;
        $filerInput
            ->expects($this->once())
            ->method('getMimeType')
            ->willReturn('text/plain')
        ;
        $filerInput
            ->expects($this->exactly(2))
            ->method('getSize')
            ->willReturn(200)
        ;
        $filterManager = $this->createMock(FilterManager::class);
        $filer = new Filer($filterManager);
        $filer->addTypeConfiguration('test_type', $this->getConfiguration());
        $this->expectException(FilerValidationException::class);
        $this->expectExceptionMessage('Invalid input given');
        $filer->saveFile($filerInput);
    }

    public function testSaveFileDocument(): void
    {
        $filterManager = $this->createMock(FilterManager::class);
        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('write')
            ->with($this->isType('string'), 'test_content', true)
            ->willReturn(10)
        ;
        $filerInput = $this->createMock(FilerInput::class);
        $filerInput
            ->expects($this->exactly(2))
            ->method('getType')
            ->willReturn('test_type')
        ;
        $filerInput
            ->expects($this->exactly(4))
            ->method('getMimeType')
            ->willReturn('text/plain')
        ;
        $filerInput
            ->expects($this->once())
            ->method('getSize')
            ->willReturn(10)
        ;
        $filerInput
            ->expects($this->once())
            ->method('getOriginalFilename')
            ->willReturn('test_original_filename')
        ;
        $filerInput
            ->expects($this->once())
            ->method('getContent')
            ->willReturn('test_content')
        ;

        $filer = new Filer($filterManager);
        $filer->addTypeConfiguration('test_type', $this->getConfiguration());
        $filer->addFileSystem('test_access', $fileSystem);
        $filerOutput = $filer->saveFile($filerInput);
        $this->assertEquals(10, $filerOutput->getSize());
        $this->assertEquals('test_original_filename', $filerOutput->getOriginalFilename());
        $this->assertEquals('test_access', $filerOutput->getAccess());
        $this->assertEquals('test_type', $filerOutput->getType());
        $this->assertEquals('text/plain', $filerOutput->getMimetype());
    }

    private function getConfiguration(bool $isImage = false): array
    {
        return [
            'folder' => 'test_folder',
            'mimeTypes' => $isImage ? ['image/png'] : ['text/plain'],
            'maxSize' => 100,
            'access' => 'test_access',
            'filters' => [],
            'applyWatermark' => false,
            'keepSource' => true,
            'source' => null,
        ];
    }
}
