<?php

namespace Wemxo\FilerBundle\Liip\Imagine\Filter\Loader;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Wemxo\FilerBundle\Imagine\Filter\ResizeCropFilterFactoryInterface;

#[AutoconfigureTag(name: 'liip_imagine.filter.loader', attributes: ['loader' => 'resize_crop'])]
class ResizeCrop implements LoaderInterface
{
    public function __construct(private ResizeCropFilterFactoryInterface $resizeCropFilterFactory)
    {
    }

    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        if (empty($options['size']) || !is_array($options['size']) || 2 !== count($options['size'])) {
            throw new \InvalidArgumentException('Invalid filter configuration !');
        }

        $size = $options['size'];
        $background = $options['background'] ?? '#FFFFFF';
        $filter = $this->resizeCropFilterFactory->create(new Box($size[0], $size[1]), $background);

        return $filter->apply($image);
    }
}
