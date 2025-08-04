<?php

namespace Wemxo\FilerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Wemxo\FilerBundle\TypeConfiguration;

class TypeConfigurationTest extends TestCase
{
    public function test(): void
    {
        $mimeTypes = ['test_mimetype'];
        $filters = ['test_filter'];
        $typeConfiguration = new TypeConfiguration(
            'test_type',
            'test_folder',
            $mimeTypes,
            90,
            'test_access',
            $filters,
            false,
            true,
            'test_source',
        );
        $this->assertEquals('test_type', $typeConfiguration->type);
        $this->assertEquals('test_folder', $typeConfiguration->folder);
        $this->assertSame($mimeTypes, $typeConfiguration->mimeTypes);
        $this->assertSame($filters, $typeConfiguration->filters);
        $this->assertEquals('test_access', $typeConfiguration->access);
        $this->assertEquals('test_source', $typeConfiguration->source);
        $this->assertFalse($typeConfiguration->applyWatermark);
        $this->assertTrue($typeConfiguration->keepSource);
        $this->assertEquals(90, $typeConfiguration->maxSize);
    }
}
