<?php

namespace Wemxo\FilerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Wemxo\FilerBundle\FilerValidationException;

class FilerValidationExceptionTest extends TestCase
{
    public function test(): void
    {
        $previous = $this->createMock(\Throwable::class);
        $errors = [];
        $exception = new FilerValidationException(
            'test_message',
            $errors,
            99,
            $previous,
        );
        $this->assertEquals('test_message', $exception->getMessage());
        $this->assertEquals(99, $exception->getCode());
        $this->assertSame($errors, $exception->getErrors());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
