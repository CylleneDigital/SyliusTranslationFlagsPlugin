# 0004. The locales list gets its flags through its field template, not its grid configuration

- Status: accepted
- Date: 2026-10-21

## Context

The name column of the locales grid can be changed in two ways: by overriding the template the
column renders, or by changing the grid configuration to point the column to another template.
Changing the configuration would need a container extension to prepend it, or a configuration file
every host imports, and could conflict with a host project that already customizes the locales
grid.

## Decision

The plugin overrides the core template of the name field, through the same templates path as the
translations accordion. The grid configuration is left untouched.

## Consequences

- The plugin keeps a single override mechanism, and no container extension.
- A host project remains free to reconfigure the locales grid.
- If a host points the name column to a template of its own, that column shows no flag.

## Related

- [0002. The templates path is registered by a compiler pass, between the host and the core](0002-templates-path-registered-by-a-compiler-pass.md)
- [0005. One shared label, copies one line away from the core](0005-one-shared-label-copies-one-line-from-the-core.md)
- [0010. No container extension, no configuration](0010-no-container-extension-no-configuration.md)
