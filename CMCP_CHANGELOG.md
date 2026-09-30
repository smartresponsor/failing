# CMCP execution journal

## 2026-09-29 — RC remediation baseline

- Task: `engine-20260930022319-failing-711128`
- Baseline HEAD: `9bc085b96f2ebc670202849da85c37f5bd8a7c6c` on `master`, aligned with `origin/master`.
- Initial worktree: only untracked `.gating/`.
- Fresh Inspecting evidence for fingerprint `633330d039755757dbc80fa9c7f85ffada223f6dabcd515e2e6b88c127e547fb`: 0 findings.
- Canon RED baseline: Canon001, Canon018, Canon020, Canon025, Canon039, Canon052.
- Repository boundary: Failing owns generic failure declarations, registry/resolution, runtime operation inventory, RFC 9457 rendering, and thin Symfony exception integration. Consumer business failure vocabulary remains outside this repository.
- Dependency contour reviewed: Objecting, Cruding, Viewing, Interfacing, Gating, Canonization. Failing must not add reverse runtime dependencies on consumer/application packages because that would violate its explicit low-level dependency direction and Canon064.
- Normative Canonization rules consulted:
  - `Canon001TechnicalRoleFirstRule.md` → move subscriber/renderer to explicit technical-role roots.
  - `Canon018ComposerIdentityMappingRule.md` → component-owned PHP declarations use `Failure*` subject prefix from `failing/failure`.
  - `Canon020TypedSymfonyRoleRootRule.md` → subscriber belongs under `EventSubscriber/`.
  - `Canon025ComponentDualRuntimeModeRule.md` → add standalone Symfony boot surfaces while retaining `FailingBundle`.
  - `Canon039PhpTestToolingRule.md` → explicit `src/` coverage population, branch coverage, persistent text summary.
  - `Canon052GatingIntegrationRule.md` → install/symlink Gating in development, production metadata dependency without local path, gate/quality scripts, artifact-only consumer `.gating/`.
- RC-critical work selected: remediate the six RED rules without expanding Failing responsibility.
- Growth work deferred: richer diagnostics/export metadata and downstream OpenAPI parity integrations.
- Material risks: preserve consumer-facing semantics while renaming internal/public Failing contract types; preserve the untracked legacy `.gating/` tree non-destructively rather than deleting it.
- Acceptance gates: composer validate, architecture guard, CS, PHPStan, PHPUnit/coverage, Gating canon, post-mutation Inspecting, Git status/branch verification.

### Status

Implementation and verification complete before Git integration.

- Canon001/020: HTTP bucket removed; subscriber now lives in `src/EventSubscriber/`, renderer in `src/Renderer/`.
- Canon018: Failing-owned operation inventory declarations use the `Failure*` subject prefix across source, tests, DI wiring, and repository documentation.
- Canon025: standalone `bin/console`, `config/bundles.php`, FrameworkBundle dependency, and local Kernel added while retaining `FailingBundle`.
- Canon039: PHPUnit explicitly covers `src/`; `test:coverage` uses PHPUnit 12 `--path-coverage` and persists `var/coverage/summary.txt`.
- Canon052: development Gating dependency/path symlink and gate/quality scripts added; production manifest declares Gating package identity without a local path repository. The previously untracked copied `.gating/` engine was preserved non-destructively under ignored `var/cmcp-preserved-gating-engine-20260929/`.
- Composer lock refreshed package-scoped for FrameworkBundle and Gating.
- Verification GREEN: composer validate; production manifest validate; PHP lint; anti-wheel guard; CS check; PHPStan max; PHPUnit 9 tests / 15 assertions; PHPUnit path coverage; standalone Symfony `about`; local Gating; RC validator canon issue count 0; post-mutation Inspecting finding count 0.
- Post-mutation Inspecting report: `D:\PhpstormProjects\www\Inspecting\.inspecting\reports\D--PhpstormProjects-www-Failing-20260930-024007.json`.
- No user-observable UI/navigation/form changes were made; behavioral browser/mobile and screenshot evidence are not applicable.
- Remaining tail: reconcile Git status, commit coherent in-scope changes, and publish current branch if remote policy allows.

