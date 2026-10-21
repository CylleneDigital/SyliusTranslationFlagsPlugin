# 0003. The compiler pass fails loudly

- Status: accepted
- Date: 2026-10-21

## Context

The compiler pass looks for the core AdminBundle's directory among the Twig loader calls. A change
in TwigBundle or in Sylius could make it impossible to find. Skipping the registration in that case
would leave the back office without flags and without any message, a regression nobody would trace
back to the plugin.

## Decision

When the core directory cannot be found, the pass throws a `LogicException` that says the plugin
cannot override its templates. It never degrades to rendering no flags.

## Consequences

- An incompatible stack stops at container compilation, with a message naming the cause.
- The application does not boot on a stack where the pass cannot find where to register the
  templates path.

## Related

- [0002. The templates path is registered by a compiler pass, between the host and the core](0002-templates-path-registered-by-a-compiler-pass.md)
