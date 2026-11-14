## [project-topology-analysis] PHP storefront topology under JS-only extractor
- Codebase/domain discoveries: repository is a PHP storefront with directory-based functionality (`api`, `assets/ajaxs`, `public/admin`, `public/client`, `public/ctv`, `cron`, `template`), while JavaScript files are mostly bundled UI libraries and not the runtime core.
- Wrong assumptions and corrections: the required `--lang javascript` engine was assumed to work on the full repository, but it reported zero modules because the app's business logic is PHP.
- Debugging dead-ends and what actually worked: the extractor output itself was conclusive; the workaround was to preserve the application's directory topology and explicitly document the PHP limitation in the artifact.
- Techniques/patterns worth reusing: treat unsupported-language roots as directory-anchored topology groups and note the engine blocker instead of omitting the application.
- Learnings consumed: none
