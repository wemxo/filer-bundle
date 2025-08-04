<?php

namespace Wemxo\FilerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Wemxo\FilerBundle\FilerInput;

class FilerInputTest extends TestCase
{
    public function test(): void
    {
        $filerInput = new FilerInput(
            'test_original_filename',
            'test_content',
            'test_mimetype',
            10,
            'test_type'
        );
        $this->assertEquals('test_original_filename', $filerInput->getOriginalFilename());
        $this->assertEquals('test_content', $filerInput->getContent());
        $this->assertEquals('test_mimetype', $filerInput->getMimeType());
        $this->assertEquals(10, $filerInput->getSize());
        $this->assertEquals('test_type', $filerInput->getType());
    }
}
