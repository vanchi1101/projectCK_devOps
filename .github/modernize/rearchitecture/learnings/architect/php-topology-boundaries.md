# PHP topology boundaries

This note captures the project-decomposition limitation when a PHP repository is analyzed through the JavaScript-only topology engine.

## What Happened
The repo at `D:\xampp\htdocs` is a PHP storefront with shared config/includes/classes and a UI layer under `template/` plus JavaScript bundles. The required project-decomposition invocation used `--lang javascript`, which only scans `.js/.jsx/.ts/.tsx` sources. The engine then reported `No modules found!`, even though the application root contains the real runtime logic.

## Takeaway
When the workflow passes a JS-only language target for a PHP app, do not silently drop the application boundaries. Treat the repo as a directory-affinity topology: group `api`, `assets/ajaxs`, `public/*`, `config`, `includes`, `class`, `lib`, and `template` as the module map, and record the language limitation as a known blocker for L1/L3 graph generation.

## History
- 2026-11-13 (D:\xampp\htdocs/project-topology): captured the PHP-vs-JS extractor mismatch and preserved the PHP application module boundaries in the topology artifact.
