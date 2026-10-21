# 0010. No container extension, no configuration

- Status: accepted
- Date: 2026-10-21

## Context

The plugin's only runtime piece is the compiler pass that registers its templates path. It has no
service to declare, no configuration to prepend in another bundle, and nothing a shop would need to
set.

## Decision

The bundle class adds the compiler pass in its `build()` method and has no container extension.
The plugin exposes no configuration, route, table or command. The bundle keeps `SyliusPluginTrait`,
as Sylius plugins do, although it currently has no effect here.

## Consequences

- Registering the bundle is the whole installation.
- A future need for services or configuration brings back an extension, as a new decision.

## Related

- [0002. The templates path is registered by a compiler pass, between the host and the core](0002-templates-path-registered-by-a-compiler-pass.md)
- [0004. The locales list gets its flags through its field template, not its grid configuration](0004-locales-list-through-its-field-template.md)
- [0009. The scope is the translations accordion and the locales list](0009-scope-limited-to-two-admin-screens.md)
