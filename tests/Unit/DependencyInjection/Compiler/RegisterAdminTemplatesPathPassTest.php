<?php

declare(strict_types=1);

namespace Tests\CylleneDigital\SyliusTranslationFlagsPlugin\Unit\DependencyInjection\Compiler;

use CylleneDigital\SyliusTranslationFlagsPlugin\DependencyInjection\Compiler\RegisterAdminTemplatesPathPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RegisterAdminTemplatesPathPassTest extends TestCase
{
    private const string HOST_OVERRIDES = '/app/templates/bundles/SyliusAdminBundle';

    private const string CORE = '/vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/templates';

    private const string PLUGIN = '/plugin/templates/admin';

    public function testThePluginPathIsInsertedRightBeforeTheCoreDirectory(): void
    {
        // The calls TwigBundle registers for a bundle whose templates the host overrides.
        $container = $this->containerWithLoaderCalls([
            ['addPath', [self::HOST_OVERRIDES, 'SyliusAdmin']],
            ['addPath', [self::CORE, 'SyliusAdmin']],
            ['addPath', [self::CORE, '!SyliusAdmin']],
        ]);

        (new RegisterAdminTemplatesPathPass(self::PLUGIN))->process($container);

        self::assertSame(
            [
                ['addPath', [self::HOST_OVERRIDES, 'SyliusAdmin']],
                ['addPath', [self::PLUGIN, 'SyliusAdmin']],
                ['addPath', [self::CORE, 'SyliusAdmin']],
                ['addPath', [self::CORE, '!SyliusAdmin']],
            ],
            $container->getDefinition('twig.loader.native_filesystem')->getMethodCalls(),
        );
    }

    public function testItFailsLoudlyWhenTheCoreDirectoryIsNotRegistered(): void
    {
        $container = $this->containerWithLoaderCalls([
            ['addPath', ['/app/templates']],
        ]);

        $this->expectException(\LogicException::class);

        (new RegisterAdminTemplatesPathPass(self::PLUGIN))->process($container);
    }

    /** @param list<array{0: string, 1: array<mixed>}> $calls */
    private function containerWithLoaderCalls(array $calls): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setDefinition('twig.loader.native_filesystem', (new Definition())->setMethodCalls($calls));

        return $container;
    }
}
