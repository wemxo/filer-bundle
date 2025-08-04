<?php

namespace Wemxo\FilerBundle\Imagine\Filter;

use Imagine\Image\BoxInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\PaletteInterface;

interface ResizeCropFilterFactoryInterface
{
    public function create(BoxInterface $size, string $background = '#FFFFFF', ?ImagineInterface $imagine = null, ?PaletteInterface $palette = null): ResizeCrop;
}
