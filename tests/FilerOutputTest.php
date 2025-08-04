<?php

namespace Wemxo\FilerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Wemxo\FilerBundle\FilerOutput;
use Wemxo\FilerBundle\ResizedFile;

class FilerOutputTest extends TestCase
{
    public function test(): void
    {
        $resizedFile = $this->createMock(ResizedFile::class);
        $filerOutput = new FilerOutput(
            'test_original_filename',
            'test_mimetype',
            'test_access',
            'test_full_name',
            'test_type',
            10,
            [$resizedFile],
        );
        $this->assertEquals('test_original_filename', $filerOutput->getOriginalFileName());
        $this->assertEquals('test_mimetype', $filerOutput->getMimeType());
        $this->assertEquals('test_access', $filerOutput->getAccess());
        $this->assertEquals('test_full_name', $filerOutput->getFullName());
        $this->assertEquals('test_type', $filerOutput->getType());
        $this->assertEquals(10, $filerOutput->getSize());
        $resizedFiles = $filerOutput->getResideFiles();
        $this->assertCount(1, $resizedFiles);
        $this->assertSame($resizedFile, $resizedFiles[0]);
    }
}
