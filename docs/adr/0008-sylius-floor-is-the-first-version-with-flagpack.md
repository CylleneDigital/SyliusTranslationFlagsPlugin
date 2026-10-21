# 0008. The Sylius floor is the first version that ships flagpack

- Status: accepted
- Date: 2026-10-21

## Context

The plugin renders flags from the `flagpack` prefix Sylius configures. On the Sylius versions
before that prefix existed, the plugin's flags do not render as intended, and on the oldest ones
rendering a `flagpack` icon throws. The oldest of those versions also render the
translations accordion from a different template.

## Decision

The plugin requires the first Sylius minor version that ships the `flagpack` icon set. It does not
support older versions, even partially.

## Consequences

- Composer refuses the plugin on a Sylius version that does not ship `flagpack`.
- A shop on an older Sylius version upgrades Sylius before installing the plugin.

## Related

- [0006. Flags come from the flagpack icon set Sylius configures](0006-flags-from-the-sylius-flagpack-set.md)
