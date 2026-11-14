# Project Topology

## Project Scale
- **Effective LOC**: 462,219 (project profile total, language-neutral; includes PHP, JavaScript, CSS, HTML, and SQL)
- **Module count**: 12 functional modules identified in the project profile; 9 top-level directory clusters used for topology grouping
- **Dependency density**: Not measurable via `decompose.py` in this repository under the required JS-only invocation; the script reports zero modules because the application logic is PHP-centric rather than a supported JavaScript/TypeScript app.
- **Language mix**: PHP is the dominant runtime and application logic; JavaScript is present mostly in `template/**` UI bundles and vendor-like frontend assets; CSS/HTML/SQL are present as static or presentation layer artifacts.

## Module Groups

| Group | Modules | Affinity Rationale (directory affinity only; no L1 edge count available) | Effective LOC | Scope | Flag |
|-------|---------|-------------------------------|---------------|-------|------|
| G1 | `config`, `includes`, `class`, `lib` | Foundation services, shared includes, and bootstrap utilities are imported by nearly every runtime request and admin action; this is the app's core support layer. | N/A — L1 unavailable for PHP root | in-scope | — |
| G2 | `api` | API endpoints and request handlers are the service boundary for wallet/card/checkout-style flows; they sit in front of the shared business logic. | N/A — L1 unavailable for PHP root | in-scope | — |
| G3 | `assets/ajaxs`, `public/admin` | Administrative workflows and AJAX handlers operate on accounts, bank data, categories, orders, and settlement actions; they form a distinct business cluster. | N/A — L1 unavailable for PHP root | in-scope | — |
| G4 | `assets/ajaxs/ctv`, `public/ctv`, `public/ctvacc` | Commission/agent/operator pathways are a separate user domain with parallel account and fulfillment workflows; they share the same core bootstrap but are logically isolated from the primary admin surface. | N/A — L1 unavailable for PHP root | in-scope | — |
| G5 | `public/client` | Client-facing storefront and purchase flow; end-user UI shell and request paths are centered here. | N/A — L1 unavailable for PHP root | in-scope | — |
| G6 | `cron` | Scheduled jobs and offline service workers (banking and MoMo reconciliations) are a separate execution path from request-driven business logic. | N/A — L1 unavailable for PHP root | in-scope | — |
| G7 | `template`, `public`, `assets/img`, `assets/storage` | Presentation layer, templates, and static assets are the UI and content boundary. This layer is coupled to the backend by server-rendered templates and AJAX calls, but its authored logic is not the runtime core. | N/A — L1 unavailable for PHP root | in-scope | — |
| G8 | `klown` | Local application-owned module directory; currently appears to be a custom-owned domain area and should be treated as a separate implementation cluster until a more specific business model is mapped. | N/A — L1 unavailable for PHP root | in-scope | — |

## Cross-group Dependencies
- G1 → G2/G3/G4/G5/G6/G7: shared bootstrap, configuration, utility helpers, and session/auth dependencies are imported by most request entry points.
- G2 → G3/G4/G5: API endpoints and service calls feed the admin and storefront interfaces.
- G3/G4/G5 → G7: each user-facing surface renders through the template and asset layer.
- G6 → G1: scheduled jobs rely on the same shared configuration and utility layer.

These dependency relationships are inferred from directory affinity and the project profile's explicit module map; they are not script-generated because `decompose.py` cannot build a PHP L1 graph in this repo.

## Critical Path
G1 → G2 → G3/G4/G5 → G7

`G1` is the composition root and shared dependency layer; request-driven modules depend on it, and the user-facing layer depends on those request handlers and templates. This path should be treated as the runtime backbone even though the exact edge counts are not available from the JS-only extractor.

## Provenance

### L1 Extract (decompose.py output summary)
- Detected language: `javascript`
- Coverage: `0 / 462,219 LOC (0.00%)`
- Modules: `0`, Edges: `0`, SCCs: `0`
- DAG layers: none
- Oversized modules: none
- Command: `python "c:\Users\ADMIN\AppData\Roaming\Code\agentPlugins\vscode-synced-customization-agent-host-copilotcli-1254287624\334866500\skills\project-decomposition\scripts\decompose.py" "D:\xampp\htdocs" --lang javascript --exclude "vendor,.git,.svn,.hg,node_modules,public_ftp"`

```text
Excluding path patterns: /vendor/, /.git/, /.svn/, /.hg/, /node_modules/, /public_ftp/
Language(s): javascript
Target extensions: ['.cjs', '.js', '.jsx', '.mjs', '.ts', '.tsx']
  [javascript] no modules found, skipping
No modules found!
```

### L2 Grouping decisions
- Path: not classified — the repository's actual runtime is PHP, and the required JS-only extractor yields zero modules. This is a language limitation rather than a topology result.
- Domain overrides: grouping uses top-level application directories as the relevant module boundaries (`api`, `assets/ajaxs`, `public/admin`, `public/client`, `public/ctv`, `cron`, `template`, `config`, `includes`, `class`, `lib`, `klown`).
- Implicit dependencies considered: shared bootstrap/configuration and helper imports are the dominant runtime coupling. No script-derived dependency graph was available.
- Adapter feasibility: not assessable without a valid PHP L1 module graph.

### L3 Validation
- Not run: the repository does not satisfy the supported language footprint for `decompose.py` under the required `--lang javascript` mode.
- The required validation gate is therefore blocked by language support, not by a failed grouping or a bad topology.

## PHP-specific limitation
- The project profile and repository structure clearly indicate a PHP storefront with static UI assets and embedded JavaScript libraries.
- The topology engine's `javascript` mode only counts `.js/.ts` files and therefore reports `no modules found` for the PHP application root.
- Because of this limitation, the topology artifact above is a directory-anchored module map rather than a fully generated L1/L3 graph. It still preserves the real application boundaries and prevents the PHP application from being silently omitted.
