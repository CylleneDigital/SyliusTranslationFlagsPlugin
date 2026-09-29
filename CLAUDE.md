# CLAUDE.md

Guide for coding agents working on this repository. It complements, without repeating them:

- [`README.md`](README.md) - what the plugin does, installation, overriding, production
  requirements
- [`CONTRIBUTING.md`](CONTRIBUTING.md) - bringing up the environment, the six QA checks, PR
  conventions

This file only holds what reading the code will not tell you.

The plugin targets Sylius `~2.2.0 || ~2.3.0`. Both overridden core templates are byte-for-byte
identical between Sylius 2.2.8 and 2.3.0. The floor is 2.2 because the `flagpack` icon set only
exists since then: on 2.1 no flag would render (`ignore_not_found: true`), and on 2.0 the
accordion template is different and `ux_icon('flagpack:…')` throws an exception.

## Where things live

| Path | Role |
|---|---|
| `templates/shared/locale_label.html.twig` | **The shared label**: `ux_icon('flagpack:' ~ country\|lower)` + capitalized locale name. Served as `@CylleneDigitalSyliusTranslationFlagsPlugin/shared/locale_label.html.twig`, takes `locale_code` |
| `templates/admin/shared/helper/translations.html.twig` | Copy of the core's translations accordion, differing by the one `accordion_header` line that includes the label |
| `templates/admin/locale/grid/field/name.html.twig` | Name column of the locales grid, rendering the label |
| `src/DependencyInjection/Compiler/RegisterAdminTemplatesPathPass.php` | Serves `templates/admin` under `@SyliusAdmin`, right before the core's `templates/` |
| `src/CylleneDigitalSyliusTranslationFlagsPlugin.php` | The bundle: adds the pass in `build()`. The plugin has no DI extension |
| `tests/Unit/` | The compiler pass alone, on a bare `ContainerBuilder` - including the `LogicException` when the core directory is missing |
| `tests/Functional/` | The two screens rendered through the HTTP kernel, the template lookup order, the label without a region |
| `tests/TestApplication/` | Throwaway Sylius application - test configuration, not plugin code. Its empty `templates/bundles/SyliusAdminBundle/` and `templates/bundles/CylleneDigitalSyliusTranslationFlagsPlugin/` stand for a host's override directories, which `PluginBootTest` checks the lookup order against |

The pass inserts the path into the `twig.loader.native_filesystem` method calls rather than going
through `twig.paths`: TwigBundle registers configured paths before every bundle path, so the plugin
would shadow the host's `templates/bundles/SyliusAdminBundle/` too, and the host could no longer
override the templates. The result is host > plugin > core.

## Commands

The six QA checks are in [`CONTRIBUTING.md`](CONTRIBUTING.md). What it does not say:

- The console is the test app's proxy: `vendor/bin/console` (no `bin/` at the project root).
- DB: the versioned `tests/TestApplication/.env` / `.env.test` carry a generic `DATABASE_URL`
  (`root@127.0.0.1/cyllene_digital_sylius_translation_flags_plugin_%kernel.environment%`, same
  scheme as SyliusAxeptaPlugin); each workstation overrides it in the gitignored `.env.dev.local` /
  `.env.test.local`. In CI, BuildTestAppAction exports its own `DATABASE_URL`, which wins over
  every `.env` file.
- The public directory served by the test app is `vendor/sylius/test-application/public`.
- The repo ships the Sylius plugin skeleton's Docker stack for contributors, same as
  SyliusAxeptaPlugin (`compose.yml` + `compose.override.dist.yml` + `Makefile`).

## Tests

Functional tests sync the schema via `SchemaTool::updateSchema` (no migrations to run before
them); they insert the `en_US`/`fr_FR` locales and an admin in `fr_FR` if missing (idempotent).

Sylius' migrations do nothing on MariaDB: each MySQL migration skips itself unless `isMySql()`
(`is_a($platform, MySQLPlatform::class)`), and DBAL 4's `MariaDBPlatform` extends
`AbstractMySQLPlatform`, not `MySQLPlatform` - `doctrine:migrations:migrate` reports
"26 migrations executed, 0 sql queries" and leaves an empty schema. Hence:

- `composer run database-reset` uses `doctrine:schema:create` (works on MariaDB);
- the `Makefile` (`make database-init|database-reset`) keeps `doctrine:migrations:migrate`: it
  targets the `mysql:8.4` service of `compose.yml`, where the migrations do run;
- in CI, BuildTestAppAction's migrations leave the MariaDB schema empty, which is harmless: the
  tests build their own schema.

## Known pitfalls

- Do not set the `TRUSTED_PROXIES` env variable: the test app's `public/index.php` uses the
  removed constant `Request::HEADER_X_FORWARDED_ALL` → fatal error. Trusted proxies are set in
  `framework` config (`tests/TestApplication/config/config.yaml`).
- The admin "notifications" widget calls gus.sylius.com (unreachable internally): disabled via
  `sylius_twig_hooks` in the same config.
- Sylius ships only 16 flagpack SVGs (`UiBundle/Resources/assets/icons/flagpack`); every other flag
  comes from the Iconify API at render time (`ux_icons.iconify.enabled`), cached in
  `cache.system`. With `ignore_not_found: true`, an offline server renders no flag and no error
  (documented in the README, "In production"). `ux:icons:import` is no workaround: it writes to
  `assets/icons/`, but `LocalSvgIconRegistry::get()` only looks up a prefix that has an
  `icon_sets` path (Sylius maps `flagpack` to its own directory) in that path.
- The override's Twig namespace is written `SyliusAdmin` (without `@`) in the `addPath` call: with
  `@`, it is a separate namespace that never resolves.
- Tests run in debug (no `APP_DEBUG=0` in `phpunit.xml.dist`): the container rebuilds by itself
  after a change to the bundle, the compiler pass or a config file. One exception: creating a
  `tests/TestApplication/templates/bundles/<Bundle>/` directory that did not exist is not
  detected, so clear `var/cache/test`. The test app declares its templates as
  `vendor/sylius/test-application/../../../tests/TestApplication/templates`, and Symfony skips
  freshness tracking for a missing path it takes to be in `vendor/`
  (`ContainerBuilder::inVendors()`, which falls back to the raw path when `realpath()` fails).
- Never set `DATABASE_URL` in the environment before running the tests: an external env variable
  would override `.env.test(.local)` and run the tests against the wrong database.
- Front build (required by the functional tests: admin pages render the Webpack Encore
  entrypoints): in `vendor/sylius/test-application`, `yarn install && yarn build`. The
  `package.json` has relative `file:../sylius/...` dependencies: run it from the package with the
  whole project visible.
- Composer (≥ 2.10) blocks installing sylius/sylius versions affected by unfixed security
  advisories: on 2.2, 2.2.0 to 2.2.5 are excluded (fixed in 2.2.6), so a `~2.2.0` constraint
  resolves to 2.2.10.

## Invariants not to break

- **The copies stay as close to the core as possible.** `translations.html.twig` differs from the
  core by one line; any new override renders the label through `locale_label.html.twig` rather than
  duplicating the flag markup, so that the next Sylius release can be followed with a plain diff
  against `vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/templates/`.
- **Host > plugin > core.** Never move the path registration to `twig.paths`, nor insert it before
  the host's override directory: a host must always be able to take a template back.
- **The pass fails loudly.** If the core's `@SyliusAdmin` directory cannot be found among the loader
  calls, it throws a `LogicException`: never degrade to "no flags" silently.
- **No region, no flag.** `sylius_locale_country` (=`\Locale::getRegion`) returns `''` for a locale
  without a region (`en`, `fr`…): the label then shows the name alone, as the core's country grid
  does.
- The public surface - the bundle class and its namespace, the overridden templates and their
  rendering, the shared label and its `locale_code` variable - only breaks in a major version, with
  an entry in `UPGRADE.md`.

## What does not belong here

A host project's own template tweaks - a different flag size, a custom locale order, a shop
override - stay in the project using them. This plugin carries the translations accordion and
locales list overrides, nothing else.
