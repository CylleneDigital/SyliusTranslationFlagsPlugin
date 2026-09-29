<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    if (str_starts_with($container->env(), 'test')) {
        // Exposed for the tests that inspect template resolution (neither service is
        // defined yet at this point, hence the aliases).
        $container->services()->alias('test.twig', 'twig')->public();
        $container->services()->alias('test.twig.loader.native_filesystem', 'twig.loader.native_filesystem')->public();
    }
};
