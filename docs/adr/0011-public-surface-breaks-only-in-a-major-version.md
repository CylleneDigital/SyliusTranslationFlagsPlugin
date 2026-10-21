# 0011. The public surface only breaks in a major version

- Status: accepted
- Date: 2026-10-21

## Context

A host project relies on the bundle class to register the plugin, on the overridden templates to
render the two screens, and on the shared label to change the flags everywhere. A host override of
the shared label stops applying, silently, if the label is renamed or moved; a host template that
includes it breaks if its variable changes.

## Decision

The public surface is the bundle class and its namespace, the overridden templates and their
rendering, and the shared label with its `locale_code` variable. It only breaks in a major
version, with an entry in the upgrade guide that gives the change to make in the host project,
including for a host override that would stop applying. The plugin states no support policy for
older versions.

## Consequences

- A minor or patch version is not meant to require a change in a host project.
- Renaming or moving an overridden template, or changing what the label receives, waits for a
  major version.

## Related

- [0005. One shared label, copies one line away from the core](0005-one-shared-label-copies-one-line-from-the-core.md)
