<?php

namespace Wemxo\FilerBundle\Tests\Liip\Imagine\Filter\Loader;

use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use PHPUnit\Framework\TestCase;
use Wemxo\FilerBundle\Imagine\Filter\ResizeCrop as ResizeCropFilter;
use Wemxo\FilerBundle\Imagine\Filter\ResizeCropFilterFactoryInterface;
use Wemxo\FilerBundle\Liip\Imagine\Filter\Loader\ResizeCrop;

class ResizeCropTest extends TestCase
{
    public function testLoadException(): void
    {
        $this->expectExceptionMessage(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid filter configuration !');
        $resizeCrop = new ResizeCrop($this->createMock(ResizeCropFilterFactoryInterface::class));
        $resizeCrop->load(
            $this->createMock(ImageInterface::class),
            []
        );
    }

    public function testLoadSuccess(): void
    {
        $backGround = '#FFFFFF';
        $resizeCropFilterFactory = $this->createMock(ResizeCropFilterFactoryInterface::class);
        $resizeCropFilter = $this->createMock(ResizeCropFilter::class);
        $image = $this->createMock(ImageInterface::class);
        $resultImage = $this->createMock(ImageInterface::class);
        $resizeCropFilterFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(BoxInterface::class), $backGround)
            ->willReturn($resizeCropFilter);

        $resizeCropFilter
            ->expects($this->once())
            ->method('apply')
            ->with($image)
            ->willReturn($resultImage);

        $resizeCrop = new ResizeCrop($resizeCropFilterFactory);
        $result = $resizeCrop->load(
            $image,
            [
                'background' => $backGround,
                'size' => [10, 10],
            ]
        );
        $this->assertSame($result, $resultImage);
    }
}
