<?php

namespace Wemxo\FilerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Wemxo\FilerBundle\ResizedFile;

class ResizedFileTest extends TestCase
{
    public function test(): void
    {
        $resizedFile = new ResizedFile('test_filter', 'test_full_name', 10);
        $this->assertEquals('test_filter', $resizedFile->filter);
        $this->assertEquals('test_full_name', $resizedFile->fullName);
        $this->assertEquals(10, $resizedFile->size);
    }
}
