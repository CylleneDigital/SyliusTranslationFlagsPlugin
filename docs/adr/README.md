# Architecture decision records

One file per decision: its context, what was decided, what follows from it. A decision is not
edited once accepted: a new record supersedes it, and the old one says so in its status.

A record holds the decision and its reasons, which do not move with the code. It names what the
decision is about (a template, a compiler pass, an icon set), nothing else of the code. What moves
with it - file paths, versions, counts - is in the [README](../../README.md), which follows the
code: a record that quoted it would be wrong at the next change, and could not be corrected. A
record explains a choice, it promises nothing: the public surface is the one
[0011](0011-public-surface-breaks-only-in-a-major-version.md) names.

A new record takes the next number, the template of the others (Status, Date, Context, Decision,
Consequences, Related, and Supersedes under the date when it replaces one) and a line in the
table below.

| # | Decision |
|---|---|
| 0001 | [Flags are rendered server side, by copies of the core templates](0001-flags-rendered-server-side-by-template-copies.md) |
| 0002 | [The templates path is registered by a compiler pass, between the host and the core](0002-templates-path-registered-by-a-compiler-pass.md) |
| 0003 | [The compiler pass fails loudly](0003-the-compiler-pass-fails-loudly.md) |
| 0004 | [The locales list gets its flags through its field template, not its grid configuration](0004-locales-list-through-its-field-template.md) |
| 0005 | [One shared label, copies one line away from the core](0005-one-shared-label-copies-one-line-from-the-core.md) |
| 0006 | [Flags come from the flagpack icon set Sylius configures](0006-flags-from-the-sylius-flagpack-set.md) |
| 0007 | [No region, no flag](0007-no-region-no-flag.md) |
| 0008 | [The Sylius floor is the first version that ships flagpack](0008-sylius-floor-is-the-first-version-with-flagpack.md) |
| 0009 | [The scope is the translations accordion and the locales list](0009-scope-limited-to-two-admin-screens.md) |
| 0010 | [No container extension, no configuration](0010-no-container-extension-no-configuration.md) |
| 0011 | [The public surface only breaks in a major version](0011-public-surface-breaks-only-in-a-major-version.md) |
