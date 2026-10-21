# 0009. The scope is the translations accordion and the locales list

- Status: accepted
- Date: 2026-10-21

## Context

Locale names appear in other places of the back office: the translations of show pages, the locale
of an order, attribute values, option values. Covering them would turn the plugin into one that
changes every locale name of the back office, with a copied core template for each.

## Decision

The plugin changes the translations accordion of the back-office forms and the locales list, nothing
else. It does not touch the shop. Settings that belong to one shop - a different flag size, a custom
locale order, a shop override - stay in that shop's project.

## Consequences

- The plugin carries few copied templates to follow from one Sylius version to the next.
- A shop that wants flags elsewhere includes the shared label in its own overrides.
- Extending the scope to another screen is a new decision.

## Related

- [0005. One shared label, copies one line away from the core](0005-one-shared-label-copies-one-line-from-the-core.md)
- [0010. No container extension, no configuration](0010-no-container-extension-no-configuration.md)
