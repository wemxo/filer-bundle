<?php

declare(strict_types=1);

namespace Wemxo\FilerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Wemxo\FilerBundle\DependencyInjection\Compiler\FilerCompilerPass;

class FilerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new FilerCompilerPass());
    }
}
