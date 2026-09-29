<?php

declare(strict_types=1);

namespace Tests\CylleneDigital\SyliusTranslationFlagsPlugin\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Renders, through the HTTP kernel, the two admin screens the plugin overrides - the
 * translations accordion (on the taxon edit form) and the locales grid - then checks
 * that every locale label shows a flag before the capitalized locale name.
 */
final class AdminLocaleLabelsTest extends WebTestCase
{
    private const string ADMIN_USERNAME = 'translation_flags_admin';

    private KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        // Sync the full test-app schema - idempotent between runs.
        (new SchemaTool($entityManager))->updateSchema($entityManager->getMetadataFactory()->getAllMetadata());

        $connection = $entityManager->getConnection();
        foreach (['en_US', 'fr_FR'] as $locale) {
            if (false === $connection->fetchOne('SELECT id FROM sylius_locale WHERE code = ?', [$locale])) {
                $connection->executeStatement('INSERT INTO sylius_locale (code, created_at, updated_at) VALUES (?, NOW(), NOW())', [$locale]);
            }
        }

        self::ensureKernelShutdown();
    }

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->client->catchExceptions(false);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $entityManager->getConnection()->executeStatement("DELETE FROM sylius_taxon WHERE code = 'translation-flags-test'");

        $this->client->loginUser($this->getAdmin($entityManager), 'admin');
    }

    public function testTheAccordionHeadersShowTheFlagBeforeTheCapitalizedLocaleName(): void
    {
        $taxon = $this->createTaxon();

        $taxonId = $taxon->getId();
        \assert(\is_int($taxonId));

        $crawler = $this->client->request('GET', sprintf('/admin/taxons/%d/edit', $taxonId));

        self::assertResponseIsSuccessful();
        $content = (string) $this->client->getResponse()->getContent();

        self::assertStringContainsString('id="taxon-translations"', $content);

        // The admin is French: Intl locale names are localized in French and start with
        // a lowercase letter - the plugin's override capitalizes the first letter.
        self::assertStringContainsString('Anglais (États-Unis)', $content);
        self::assertStringContainsString('Français (France)', $content);
        self::assertStringNotContainsString('anglais (États-Unis)', $content);
        self::assertStringNotContainsString('français (France)', $content);

        $buttons = $crawler->filter('#taxon-translations .accordion-item > .accordion-button');
        self::assertCount(2, $buttons);

        $names = [];
        $buttons->each(function (Crawler $button) use (&$names): void {
            // The flag is the button's only direct <svg> child (the chevron lives in
            // .accordion-button-toggle); it has no .flag class, which targets Tabler's CSS flags.
            self::assertCount(1, $button->children('svg'), 'Every accordion header must contain exactly one flag');
            self::assertStringStartsWith(
                '<svg',
                ltrim($button->html()),
                'The flag must come before the locale name',
            );
            $names[] = trim($button->text());
        });

        sort($names);
        self::assertSame(['Anglais (États-Unis)', 'Français (France)'], $names);
    }

    public function testTheLocalesGridShowsTheFlagBeforeTheCapitalizedLocaleName(): void
    {
        $crawler = $this->client->request('GET', '/admin/locales/');

        self::assertResponseIsSuccessful();

        $labels = $crawler->filter('table tbody td span.fw-medium');
        self::assertGreaterThanOrEqual(2, $labels->count());

        $names = [];
        $labels->each(function (Crawler $label) use (&$names): void {
            self::assertCount(1, $label->children('svg'), 'Every locale name must carry exactly one flag');
            self::assertStringStartsWith('<svg', ltrim($label->html()), 'The flag must come before the locale name');
            $names[] = trim($label->text());
        });

        self::assertContains('Anglais (États-Unis)', $names);
        self::assertContains('Français (France)', $names);
    }

    private function getAdmin(EntityManagerInterface $entityManager): AdminUserInterface
    {
        $container = self::getContainer();

        /** @var RepositoryInterface<AdminUserInterface> $adminUserRepository */
        $adminUserRepository = $container->get('sylius.repository.admin_user');
        $admin = $adminUserRepository->findOneBy(['username' => self::ADMIN_USERNAME]);

        if (null === $admin) {
            /** @var FactoryInterface<AdminUserInterface> $adminUserFactory */
            $adminUserFactory = $container->get('sylius.factory.admin_user');
            $admin = $adminUserFactory->createNew();
            $admin->setUsername(self::ADMIN_USERNAME);
            $admin->setEmail(self::ADMIN_USERNAME . '@example.com');
            $admin->setPassword('not-used-logged-in-programmatically');
            $admin->setLocaleCode('fr_FR');
            $admin->setEnabled(true);

            $entityManager->persist($admin);
            $entityManager->flush();
        }

        return $admin;
    }

    private function createTaxon(): TaxonInterface
    {
        $container = self::getContainer();

        /** @var FactoryInterface<TaxonInterface> $taxonFactory */
        $taxonFactory = $container->get('sylius.factory.taxon');
        $taxon = $taxonFactory->createNew();
        $taxon->setCode('translation-flags-test');
        $taxon->setEnabled(true);
        $taxon->setPosition(1);

        $translation = new TaxonTranslation();
        $translation->setLocale('en_US');
        $translation->setName('Test taxon');
        $translation->setSlug('test-taxon');
        $taxon->addTranslation($translation);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        $entityManager->persist($taxon);
        $entityManager->flush();

        return $taxon;
    }
}
