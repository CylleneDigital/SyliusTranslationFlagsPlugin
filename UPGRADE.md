# Upgrade guide

This file lists, one major version at a time, what has to change in a project already using the
plugin.

The plugin overrides two back-office templates: a compatibility break means the translations
accordion or the locales list no longer renders the way the host expects, or a host project's
override of them, or of the shared label, stops applying - silently. Every break must therefore be
written here before it is released, together with the exact manoeuvre to carry out.

`v1.0.0` is the first release: there is nothing to upgrade from yet.
