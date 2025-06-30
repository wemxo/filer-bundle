<?php

namespace Wemxo\FilerBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use Wemxo\FilerBundle\DependencyInjection\Configuration;
use Wemxo\FilerBundle\Filer;

class FilerCompilerPass implements CompilerPassInterface
{
    /**
     * @throws \Exception
     */
    public function process(ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configs = $processor->processConfiguration(new Configuration(), $container->getExtensionConfig('filer'));
        $filerDefinition = $container->getDefinition(Filer::class);
        $enabledAccesses = $this->processAccesses($filerDefinition, $container, $configs['accesses']);
        $this->processFilerConfigs($filerDefinition, $container, $configs['types'], $enabledAccesses);
    }

    private function processFilerConfigs(Definition $filerDefinition, ContainerBuilder $container, array $configuration, array $enabledAccesses): void
    {
        foreach ($configuration as $type => $typeConfiguration) {
            if (!in_array($typeConfiguration['access'] ?? '', $enabledAccesses)) {
                throw new \InvalidArgumentException('Invalid access given !');
            }

            $filerDefinition->addMethodCall('addTypeConfiguration', [$type,
                [
                    'access' => $typeConfiguration['access'],
                    'mimeTypes' => $typeConfiguration['mime_types'],
                    'folder' => $typeConfiguration['folder'],
                    'filters' => $typeConfiguration['filters'] ?? [],
                    'maxSize' => $typeConfiguration['max_size'],
                    'applyWatermark' => $typeConfiguration['apply_watermarK'],
                    'keepSource' => $typeConfiguration['keep_source'],
                    'source' => $typeConfiguration['source'],
                ],
            ]);
        }
    }

    /**
     * @throws \Exception
     */
    private function processAccesses(Definition $filerDefinition, ContainerBuilder $container, array $configuration): array
    {
        $enabledAccess = [];
        foreach ($configuration as $access => $fileSystemId) {
            if (!$container->has($fileSystemId)) {
                throw new ServiceNotFoundException($fileSystemId);
            }

            $filerDefinition->addMethodCall('addFileSystem', [$access, new Reference($fileSystemId)]);
            $enabledAccess[] = $access;
        }

        return $enabledAccess;
    }
}
