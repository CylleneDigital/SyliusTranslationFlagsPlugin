# 0006. Flags come from the flagpack icon set Sylius configures

- Status: accepted
- Date: 2026-10-21

## Context

Sylius configures Symfony UX Icons with a `flagpack` prefix mapped to its own directory, which
holds the flags of a few countries; the core countries grid renders its flags from it. UX Icons
fetches any other icon from the Iconify API at render time and keeps it in the system cache, and
Sylius configures it to ignore icons that cannot be found.

Importing flags with `ux:icons:import` does not make them local: the command writes into the
application's icons directory, while UX Icons only looks up a `flagpack` icon in the directory
Sylius maps to that prefix. `ux:icons:lock` cannot see an icon name built at render time. Shipping
the whole flagpack set under a prefix of the plugin's own would be a different design.

## Decision

The label renders the `flagpack` icon of the locale's country, with the configuration Sylius
provides. The plugin adds no icon set and no icon configuration of its own.

## Consequences

- The flags match the icons of the Sylius admin, under the flagpack licence.
- A flag Sylius does not ship is fetched from the Iconify API the first time it is rendered after a
  cache clear.
- A server that cannot reach that API fails to render a page holding such a flag, until the icon is
  in the cache: ignoring icons that cannot be found does not cover a network failure.

## Related

- [0005. One shared label, copies one line away from the core](0005-one-shared-label-copies-one-line-from-the-core.md)
- [0007. No region, no flag](0007-no-region-no-flag.md)
- [0008. The Sylius floor is the first version that ships flagpack](0008-sylius-floor-is-the-first-version-with-flagpack.md)
