<?php

declare(strict_types=1);

namespace CylleneDigital\SyliusTranslationFlagsPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registers the plugin's templates under the @SyliusAdmin namespace, right before the core
 * AdminBundle's own directory - hence after the host's templates/bundles/SyliusAdminBundle.
 *
 * Going through `twig.paths` instead would put the plugin before the host's override too: TwigBundle
 * registers the configured paths before any bundle path, and a host could no longer take the
 * template back.
 */
final class RegisterAdminTemplatesPathPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $templatesPath,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        $loader = $container->getDefinition('twig.loader.native_filesystem');

        /** @var list<array{0: string, 1: array<mixed>, 2?: bool}> $calls */
        $calls = $loader->getMethodCalls();

        // TwigBundle ends each bundle's registration with its own templates directory under "!<namespace>".
        $coreTemplatesPath = null;
        foreach ($calls as [$method, $arguments]) {
            if ('addPath' === $method && '!SyliusAdmin' === ($arguments[1] ?? null)) {
                $coreTemplatesPath = $arguments[0];
            }
        }

        foreach ($calls as $index => [$method, $arguments]) {
            if ('addPath' === $method && [$coreTemplatesPath, 'SyliusAdmin'] === $arguments) {
                array_splice($calls, $index, 0, [['addPath', [$this->templatesPath, 'SyliusAdmin']]]);
                $loader->setMethodCalls($calls);

                return;
            }
        }

        throw new \LogicException('The Sylius AdminBundle templates directory is not registered on the Twig filesystem loader: the plugin cannot override its templates.');
    }
}
