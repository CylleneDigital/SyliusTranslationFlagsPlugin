# 0007. No region, no flag

- Status: accepted
- Date: 2026-10-21

## Context

A flag stands for a country, and the plugin takes it from the region of the locale code. A locale
without a region, such as a bare language code, has no country: any flag chosen for it would be a
guess, and a wrong one for many of its speakers.

## Decision

When the locale has no region, the label shows the capitalized locale name alone.

## Consequences

- No flag is ever attributed to a language.
- In one accordion, locales with and without a region are not aligned the same way.

## Related

- [0006. Flags come from the flagpack icon set Sylius configures](0006-flags-from-the-sylius-flagpack-set.md)
