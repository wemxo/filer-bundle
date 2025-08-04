<?php

namespace Wemxo\FilerBundle\Tests\Imagine\Filter;

use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\PaletteInterface;
use PHPUnit\Framework\TestCase;
use Wemxo\FilerBundle\Imagine\Filter\ResizeCrop;

class ResizeCropTest extends TestCase
{
    public function testConstruct(): void
    {
        $size = $this->createMock(BoxInterface::class);
        $background = '#FFFAAA';
        $resizeCrop = new ResizeCrop($size, $background);
        $reflectionClass = new \ReflectionClass($resizeCrop);
        $reflectionPropertyBackground = $reflectionClass->getProperty('background');
        $reflectionPropertySize = $reflectionClass->getProperty('size');
        $reflectionPropertyBackground->setAccessible(true);
        $reflectionPropertySize->setAccessible(true);
        $this->assertEquals($background, $reflectionPropertyBackground->getValue($resizeCrop));
        $this->assertSame($size, $reflectionPropertySize->getValue($resizeCrop));
    }

    public function testApply(): void
    {
        $size = $this->createMock(BoxInterface::class);
        $size
            ->expects($this->exactly(2))
            ->method('getWidth')
            ->willReturn(10)
        ;
        $size
            ->expects($this->exactly(2))
            ->method('getHeight')
            ->willReturn(10)
        ;
        $image = $this->createMock(ImageInterface::class);
        $resultImage = $this->createMock(ImageInterface::class);
        $image
            ->expects($this->once())
            ->method('thumbnail')
            ->with($size, ImageInterface::THUMBNAIL_FLAG_UPSCALE)
            ->willReturn($image)
        ;
        $imageSize = $this->createMock(BoxInterface::class);
        $imageSize
            ->expects($this->once())
            ->method('getWidth')
            ->willReturn(5)
        ;
        $imageSize
            ->expects($this->once())
            ->method('getHeight')
            ->willReturn(5)
        ;
        $image
            ->expects($this->exactly(2))
            ->method('getSize')
            ->willReturn($imageSize)
        ;
        $resultImage
            ->expects($this->once())
            ->method('paste')
            ->with($image)
        ;
        $color = $this->createMock(ColorInterface::class);
        $imagine = $this->createMock(ImagineInterface::class);
        $palette = $this->createMock(PaletteInterface::class);
        $palette
            ->expects($this->once())
            ->method('color')
            ->with('#FFFFFF')
            ->willReturn($color)
        ;
        $imagine
            ->expects($this->once())
            ->method('create')
            ->with($size, $color)
            ->willReturn($resultImage)
        ;
        $resizeCrop = new ResizeCrop($size, '#FFFFFF', $imagine, $palette);
        $result = $resizeCrop->apply($image);
        $this->assertSame($resultImage, $result);
    }
}
