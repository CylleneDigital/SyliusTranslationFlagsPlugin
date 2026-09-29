<?php

declare(strict_types=1);

namespace CylleneDigital\SyliusTranslationFlagsPlugin;

use CylleneDigital\SyliusTranslationFlagsPlugin\DependencyInjection\Compiler\RegisterAdminTemplatesPathPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CylleneDigitalSyliusTranslationFlagsPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterAdminTemplatesPathPass($this->getPath() . '/templates/admin'));
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
