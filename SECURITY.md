# Security policy

This plugin renders two templates in the back office: it reads no data, stores nothing and handles
no credential. A flaw here would be a rendering or template-injection issue, not a data leak -
but please still report it privately.

## Reporting a vulnerability

**Do not open a public issue.** Use either of the two private channels:

- [GitHub security advisory](https://github.com/CylleneDigital/SyliusTranslationFlagsPlugin/security/advisories/new)
  ("Report a vulnerability")
- email to sylius@groupe-cyllene.com

Please include the plugin version, the Sylius and PHP versions, and the steps to reproduce.

## Response time

First response within **5 working days**. We keep you posted on the analysis, then on the fix and
its release date.

## Supported versions

| Version | Support |
|---|---|
| `1.x` | Bug and security fixes |

## Disclosure

Coordinated disclosure: the fix is released first, then the advisory. We are happy to credit the
reporter, unless they ask otherwise.
