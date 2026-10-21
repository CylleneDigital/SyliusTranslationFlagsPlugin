# 0001. Flags are rendered server side, by copies of the core templates

- Status: accepted
- Date: 2026-10-21

## Context

Sylius renders the locale name of a translations accordion in a Twig macro that every translations
form imports, and the name of a locale in a grid field template. The macro holds no hook: reaching
it through Twig hooks would mean replacing the translations section of each form one by one, and
the locales grid only offers the template of its column.

Adding the flags in the browser, from a Stimulus controller, would avoid overriding anything, but
the flag icons would have to be exposed to JavaScript, the label would change after the page is
displayed, and the capitalization of the name would move to JavaScript too.

## Decision

The plugin overrides the two core templates with copies that render the flag and the capitalized
name in the HTML sent by the server. It ships no JavaScript and no stylesheet.

## Consequences

- The label is in the page from the first render, in every translations form at once.
- The copies must follow the core templates from one Sylius version to the next.
- A host project overriding the same templates replaces the plugin's copies.

## Related

- [0002. The templates path is registered by a compiler pass, between the host and the core](0002-templates-path-registered-by-a-compiler-pass.md)
- [0005. One shared label, copies one line away from the core](0005-one-shared-label-copies-one-line-from-the-core.md)
