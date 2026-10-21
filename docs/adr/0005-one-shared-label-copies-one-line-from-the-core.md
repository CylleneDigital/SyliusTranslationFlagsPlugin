# 0005. One shared label, copies one line away from the core

- Status: accepted
- Date: 2026-10-21

## Context

Both overridden screens show the same label: a flag, then the capitalized locale name. Writing that
markup in each copy would duplicate it, and make each copy drift further from the core template it
replaces, which then has to be followed by hand at every Sylius version.

## Decision

The label lives in a template of its own, in the plugin's namespace, and takes the locale code. Each
overridden template is a copy of the core one that only replaces the rendering of the locale name
with an include of that label, plus the markup that lays the flag out beside the name. Any new
override renders the label the same way.

## Consequences

- Following a new Sylius version is a plain diff between each copy and the core template.
- A host project changes the label everywhere by overriding the plugin's label template, or one
  screen by overriding that screen's template.
- The label template and its `locale_code` variable are part of the public surface.

## Related

- [0001. Flags are rendered server side, by copies of the core templates](0001-flags-rendered-server-side-by-template-copies.md)
- [0004. The locales list gets its flags through its field template, not its grid configuration](0004-locales-list-through-its-field-template.md)
- [0011. The public surface only breaks in a major version](0011-public-surface-breaks-only-in-a-major-version.md)
