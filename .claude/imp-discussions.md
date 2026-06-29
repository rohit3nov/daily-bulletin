# Important Discussions

A running log of significant decisions, context, and mentoring discussions for this
project. Newest entries at the top.

---

## 2026-06-29 — Working agreement & Claude tooling setup

**Context / who I am.** Rohit is a senior developer whose goal is to become a
technical architect. Personal projects live under `C:\Users\rohit\projects`; many
more will be built the same way, with Claude's help. `daily-bulletin` is the first.

**How Claude should work here.** Act as a mentor and senior technical architect, not
just an implementer:
- Explain the architectural *why* and name the pattern/concept behind a suggestion.
- Surface trade-offs and offer alternatives with pros/cons on real design decisions.
- Push back on weak designs and on **over-engineering** — prefer the simplest design
  the problem genuinely needs. Architecture is judgment, not a checklist of patterns
  to sprinkle in.

**Tooling concepts established.**
- `CLAUDE.md` (repo root) — stable project truth, auto-loaded every session.
- `.claude/settings.json` — team-shared permissions/config; `settings.local.json` —
  personal overrides (gitignored).
- `.claude/skills/` — lazy-loaded repeatable procedures (add when real repetition
  appears, not before).
- `.claude/agents/`, `.claude/commands/` — custom subagents and slash commands.
- Claude's own cross-session **memory** lives outside the repo (under Claude's config
  dir), and stores facts about the user/work, distinct from the repo-owned files above.

**Environment gotcha (resolved).** Claude runs as Windows user `workstars`, but the
project sits under `C:\Users\rohit\` which granted other users read-only. Fixed by
granting modify rights via `icacls`. Long-term, consider hosting projects outside a
personal user profile (e.g. `C:\dev\`) to avoid the account boundary.

**Next step.** Deep architectural review of the current codebase → prioritized
findings (strengths, risks, highest-leverage improvements) as the baseline to
mentor from.
