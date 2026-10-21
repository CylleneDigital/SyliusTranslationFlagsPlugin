# 0002. The templates path is registered by a compiler pass, between the host and the core

- Status: accepted
- Date: 2026-10-21

## Context

The overridden templates must be found under the `@SyliusAdmin` namespace before the core ones.
Paths configured under `twig.paths` are registered before every bundle path, including the
application's own override directory for a bundle: a plugin path set there would shadow the host's
overrides too, and a host could no longer take a template back. Documenting that limitation would
leave a public plugin with templates its users cannot override.

## Decision

A compiler pass inserts the plugin's directory into the calls of the Twig filesystem loader, under
`@SyliusAdmin`, right before the core AdminBundle's own directory. The lookup order is host, then
plugin, then core.

## Consequences

- A host project overrides a plugin template the usual Symfony way, as it would a core one.
- The pass depends on how TwigBundle registers bundle paths on the loader, a detail internal to
  TwigBundle.
- Registering the path in `twig.paths`, or before the host's override directory, would reverse
  this decision.

## Related

- [0001. Flags are rendered server side, by copies of the core templates](0001-flags-rendered-server-side-by-template-copies.md)
- [0003. The compiler pass fails loudly](0003-the-compiler-pass-fails-loudly.md)
- [0010. No container extension, no configuration](0010-no-container-extension-no-configuration.md)
