<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('Wemxo\\FilerBundle\\', '../src/')
        ->exclude([
            '../src/DependencyInjection/',
            '../src/FilerException.php',
            '../src/FilerValidationException.php',
            '../src/FilerInput.php',
            '../src/FilerOutput.php',
            '../src/TypeConfiguration.php',
            '../src/ResizedFile.php',
            '../src/FilerBundle.php',
        ]);
};