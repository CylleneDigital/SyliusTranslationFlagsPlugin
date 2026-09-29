<?php

declare(strict_types=1);

namespace Tests\CylleneDigital\SyliusTranslationFlagsPlugin\Functional;

use Sylius\Bundle\AdminBundle\SyliusAdminBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Boots the Sylius test application with the plugin enabled - no database needed - and checks
 * how templates resolve: the core translations helper is served by the plugin, a host override
 * directory is looked up before the plugin (for @SyliusAdmin and for the shared label), and the
 * shared label renders no flag for a locale without a region.
 */
final class PluginBootTest extends KernelTestCase
{
    public function testTheTranslationsHelperTemplateIsServedByThePlugin(): void
    {
        self::bootKernel();

        /** @var Environment $twig */
        $twig = self::getContainer()->get('test.twig');

        $source = $twig->getLoader()->getSourceContext('@SyliusAdmin/shared/helper/translations.html.twig');

        self::assertSame(
            \dirname(__DIR__, 2) . '/templates/admin/shared/helper/translations.html.twig',
            $source->getPath(),
        );
    }

    /**
     * The test application ships an empty templates/bundles/SyliusAdminBundle directory, the
     * one a host project puts its own overrides in: it must be looked up before the plugin.
     */
    public function testAHostOverrideIsLookedUpBeforeThePluginAndThePluginBeforeTheCore(): void
    {
        self::bootKernel();

        /** @var FilesystemLoader $loader */
        $loader = self::getContainer()->get('test.twig.loader.native_filesystem');

        $hostOverrides = (string) realpath(\dirname(__DIR__) . '/TestApplication/templates/bundles/SyliusAdminBundle');
        $plugin = (string) realpath(\dirname(__DIR__, 2) . '/templates/admin');
        $core = (string) realpath(\dirname((string) (new \ReflectionClass(SyliusAdminBundle::class))->getFileName()) . '/templates');

        $paths = array_map(static fn (string $path): string => (string) realpath($path), $loader->getPaths('SyliusAdmin'));

        self::assertSame(
            [$hostOverrides, $plugin, $core],
            array_values(array_intersect($paths, [$hostOverrides, $plugin, $core])),
        );
    }

    /**
     * A locale without a region (`en`) has no country, hence no flag: the label is the name
     * alone, as in the core's country grid. `fr_FR` is the control case.
     */
    public function testTheSharedLabelShowsNoFlagForALocaleWithoutARegion(): void
    {
        self::bootKernel();

        /** @var Environment $twig */
        $twig = self::getContainer()->get('test.twig');
        $template = '@CylleneDigitalSyliusTranslationFlagsPlugin/shared/locale_label.html.twig';

        $withoutRegion = trim($twig->render($template, ['locale_code' => 'en']));
        self::assertStringNotContainsString('<svg', $withoutRegion);
        self::assertSame('English', $withoutRegion);

        $withRegion = trim($twig->render($template, ['locale_code' => 'fr_FR']));
        self::assertStringStartsWith('<svg', $withRegion);
        self::assertStringEndsWith('French (France)', $withRegion);
    }

    /**
     * The shared locale label lives in the plugin's own namespace: the README tells a host to
     * override it under templates/bundles/CylleneDigitalSyliusTranslationFlagsPlugin, shipped
     * empty by the test application.
     */
    public function testAHostOverrideOfTheSharedLabelIsLookedUpBeforeThePlugin(): void
    {
        self::bootKernel();

        /** @var FilesystemLoader $loader */
        $loader = self::getContainer()->get('test.twig.loader.native_filesystem');

        $paths = array_map(static fn (string $path): string => (string) realpath($path), $loader->getPaths('CylleneDigitalSyliusTranslationFlagsPlugin'));

        self::assertSame(
            [
                (string) realpath(\dirname(__DIR__) . '/TestApplication/templates/bundles/CylleneDigitalSyliusTranslationFlagsPlugin'),
                (string) realpath(\dirname(__DIR__, 2) . '/templates'),
            ],
            $paths,
        );

        self::assertSame(
            \dirname(__DIR__, 2) . '/templates/shared/locale_label.html.twig',
            $loader->getSourceContext('@CylleneDigitalSyliusTranslationFlagsPlugin/shared/locale_label.html.twig')->getPath(),
        );
    }
}
