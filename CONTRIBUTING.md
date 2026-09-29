# Contributing

Thanks for your interest in this plugin. Issues and pull requests are welcome.

**A security flaw is not reported through an issue** - see [SECURITY.md](SECURITY.md).

## Setting up the development environment

The plugin is tested against a throwaway Sylius application (`sylius/test-application`),
brought up by Docker.

```bash
make init            # compose.override.yml, composer install, front-end build, containers
make database-init   # database and migrations
make load-fixtures   # Sylius sample data (optional)
```

Without Docker, with a local PHP (8.3+), a reachable MariaDB or MySQL database, and Node for
the front-end build:

```bash
composer install
composer run test-app-init   # database, schema, fixtures, front-end build
```

The versioned `tests/TestApplication/.env` and `.env.test` carry a generic `DATABASE_URL`
(`root@127.0.0.1`). To point at your own database, override it in
`tests/TestApplication/.env.test.local` (and `.env.dev.local` for the dev site) - both are
gitignored. The tests also create the schema themselves, so an empty database is enough.

## Proposing a change

`main` is protected. Every change goes through a pull request, a one-line documentation fix
included, and the maintainers work the same way.

1. **Fork** the repository, then clone your fork.
2. Branch off `main`: `git switch -c fix/short-description`.
3. Commit in English, conventional form: `feat:`, `fix:`, `docs:`, `ci:`, `chore:`.
4. Run the checks of the next section. They are the ones the CI runs.
5. Push to your fork and open a pull request against `main`.

Two checks have to be green before a pull request can be merged:

| Check | What it covers |
|---|---|
| **`Build complete`** | the matrix jobs, aggregated: PHP 8.3 to 8.5, Sylius 2.2 and 2.3, MariaDB |
| **`Composer audit`** | known vulnerabilities in the dependencies |

If this is your first contribution, the workflows will not start until a maintainer approves
the run. That is GitHub's default on public repositories, not something you did wrong.

## What has to pass before a pull request

The six checks the CI runs:

```bash
vendor/bin/phpunit --colors=always
vendor/bin/phpstan analyse --ansi --no-progress
vendor/bin/ecs check --ansi --no-progress-bar
vendor/bin/console lint:container --env=test
vendor/bin/console lint:twig templates --env=test
composer validate --ansi --strict
```

The Docker equivalents exist for the first three: `make phpunit`, `make phpstan`, `make ecs`.

`vendor/bin/ecs check --fix` fixes the style automatically. The standard is
[`sylius-labs/coding-standard`](https://github.com/Sylius-Labs/CodingStandard), the one used by
Sylius plugins, do not add personal rules to it.

## Conventions

- **PHPStan at level `max`** on `src/`, `tests/Unit/` and `tests/Functional/`. A pull request
  lowering the level or adding an `ignoreErrors` entry must explain why in its description.
- **Tests are mandatory** for any bug fix: the test must fail before the fix.
- **`UPGRADE.md`** updated if the pull request breaks compatibility. The public surface - the
  bundle class and its namespace, the overridden templates and their rendering, and the shared
  label host projects may override
  (`@CylleneDigitalSyliusTranslationFlagsPlugin/shared/locale_label.html.twig` and its
  `locale_code` variable) - only breaks in a major version.
- The codebase is written in **English**, comments and commit messages included.

## What does not belong here

A host project's own template tweaks - a different flag size, a custom locale order, a shop
override - stay in the project using them. This plugin carries the translations accordion and
locales list overrides, nothing else.
