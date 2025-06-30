<?php

namespace Wemxo\FilerBundle\Imagine\Filter;

use Imagine\Filter\FilterInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;

class ResizeCrop implements FilterInterface
{
    private string $background;

    private Box $size;

    public function __construct(Box $size, string $background = '#FFFFFF')
    {
        $this->background = $background;
        $this->size = $size;
    }

    public function apply(ImageInterface $image): ImageInterface
    {
        $image = $image->thumbnail($this->size, ImageInterface::THUMBNAIL_FLAG_UPSCALE);
        $palette = new RGB();
        $color = $palette->color($this->background);
        $imagickAdapter = new Imagine();
        $resultImage = $imagickAdapter->create($this->size, $color);
        $widthR = $image->getSize()->getWidth();
        $heightR = $image->getSize()->getHeight();
        $startX = $startY = 0;
        if ($widthR < $this->size->getWidth()) {
            $startX = ($this->size->getWidth() - $widthR) / 2;
        }

        if ($heightR < $this->size->getHeight()) {
            $startY = ($this->size->getHeight() - $heightR) / 2;
        }

        $resultImage->paste($image, new Point($startX, $startY));

        return $resultImage;
    }
}
