---
name: agent-loop-task-start
description: Start a governed agent-loop task in the current repository, define and approve a sealed task Contract, create session working memory, and compile deterministic recall/L2 context.
---

# Agent Loop Task Start

Use this skill when beginning a task in a repository that has `agent-loop`
installed and you need to define durable task intent, approve that exact
revision, create session working memory, and compile a Recall briefing from the
sealed input before editing code.

## Fast Path

Prefer the governed Contract path:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow plan <task-id> \
  --by <actor> \
  --file <path-to-file-1> \
  --file <path-to-file-2> \
  --goal "Implement the approved task." \
  --non-goal "Do not widen the task without a revised brief." \
  --acceptance "The required user-visible outcome remains present." \
  --validation "vendor/bin/phpunit tests/FocusedTest.php"
```

`workflow plan` creates or revises a candidate Contract. It deliberately creates
neither a Session nor a Run and does **not** compile Recall yet.

### Re-planning an active governed Run

`PLAN` re-entry is a supersession handoff, not an in-place mutation of the
current Run. If scope or task policy changes while a governed Session is active:

1. stop using the old Contract as implementation authority;
2. close that Session with `session close <session-id> --status dropped`;
3. rerun `workflow plan <task-id> ...` with the corrected durable intent;
4. obtain named human approval for the new Contract revision;
5. let `workflow approve` create the replacement Session/Run and archive the
   superseded Run evidence.

If durable intent did **not** change, continue the existing Run instead. Never
close a Session merely to bypass a gate, and never reuse one Session across two
Contract revisions.

When ranked map evidence is expected, establish both the semantic map and its
separate Search index **before approval**:

```bash
tools/agent-loop/vendor/bin/agent-loop map build --paths=src,tests
tools/agent-loop/vendor/bin/agent-loop map search-index build
tools/agent-loop/vendor/bin/agent-loop map summary
```

`map build` does not create `<map-root>/search.sqlite`. `workflow approve`
compiles governed Recall immediately, so an index built afterwards cannot
contribute ranked map evidence to that first briefing. Resolve `map_root` with
`tools/agent-loop/vendor/bin/agent-loop init paths --format=json` instead of assuming its location.

A named human must approve the exact revision before implementation; approval
prepares the governed Run/Session and compiles Recall from that sealed Contract.
Inspect the result immediately:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow approve <task-id> --by <human-actor>
tools/agent-loop/vendor/bin/agent-loop workflow context <task-id> --max-lines 120 --max-bytes 12000
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id>
```

## Preserve Acceptance Intent

Use repeatable `--acceptance` values for outcomes the completed task must still
make true. Keep them distinct from the other Contract fields:

- **acceptance criterion** = a required outcome or condition from the task;
- **validation command** = an executable observation used to measure reality;
- **behavior anchor** = the concrete runtime/request/consumer seam that should be
  inspected when behavior changes.

For example, `"installed agent guidance mentions the new control"` is an
acceptance criterion; `composer ci` is validation; `SessionStart -> injected
agent-loop-discipline` is a behavior anchor.

Criteria are durable task intent, **not evidence that they passed**. Do not add a
checkbox/status merely because a criterion exists. Review the criteria against
actual evidence before treating the task as complete.

Do not infer acceptance criteria from issue prose inside deterministic commands.
When a requirement matters to completion, make it explicit at PLAN time so it
survives approval, Recall, status, and review rather than depending on chat
memory.

## Historical Context Preflight

Before opening a non-trivial, repeated, or failure-driven task, use `ctx` if it
is installed to check whether prior local agent sessions contain relevant
decisions, failed attempts, commands, or review context:

```bash
ctx status
ctx sources
ctx search "<task / module / error / command>"
ctx show event <ctx-event-id> --window 5
```

Use ctx as historical source material only. It does not replace `workflow plan`,
`workflow approve`, current Recall artifacts, current repository inspection, or
validation. If ctx material affects a finding, cite it as bounded
`agent_history_reference` evidence with inspected IDs and a summary; do not paste
raw transcripts.

## Existing Work Preflight

**Inspect overlap before invention.** Before designing a new implementation for a
non-trivial task, use repository/tracker history when the host exposes it and
parallel or prior work is plausible.

1. Search a bounded set of open and recent merged/closed work for the same task,
   behavior, owner surface, or intended acceptance outcome.
2. Classify each relevant candidate as already landed, active, superseded or
   abandoned, or materially independent. An open PR is not correctness evidence.
3. Select the strongest existing candidate and try to **falsify it** against the
   current approved acceptance criteria, current source, deterministic tests,
   CI/runtime evidence, and known regressions.
4. If it already satisfies or nearly satisfies the task, reuse, repair, rebase,
   merge, or close superseded work instead of creating a competing implementation.
5. Create a new competing implementation only when evidence shows the existing
   candidate cannot satisfy the current contract or addresses a materially
   different problem.

Do not block implementation merely because external history is unavailable.
Record that overlap is unknown and continue from current repository evidence.
Do not turn this preflight into a new lifecycle state, benchmark service, or
requirement to inspect unrelated repository history.

## Task ID

Use the ticket or issue id from your board (e.g. `ABC-123`, `PROJ-42`).
If no external id exists, choose a stable local id such as `LOCAL-001` and
keep it for the life of the task. Do not generate a new one on each run.
Ask the host workflow or board if you are unsure what id to use.

## Choosing Files

Select files intentionally. Recall compiles context from what you pass; it
does not dump the entire repository into the briefing.

Good candidates:

- the task description file (`tasks/ABC-123.md`)
- the failing test or the test that covers the change
- the implementation file most directly affected
- architecture or decision notes that constrain the change
- the relevant skill or doc if guidance is part of the scope

Pass a small set of relevant files with repeated `--file` options instead of
trying to summarize the whole repository. Do not pass every file.

The initial `--file` values become the approved scope unless explicit
`--scope` values replace them. A later plan revision clears approval, so obtain
a new approval before working outside the current scope or changing required
acceptance intent.

## Map Maintenance After Start

The initial map/Search preflight belongs before approval, as shown in the fast
path above. After a branch switch or source change, keep the semantic map current
without paying for a full rebuild:

```bash
tools/agent-loop/vendor/bin/agent-loop map refresh
tools/agent-loop/vendor/bin/agent-loop map stale
```

`map refresh` re-analyses only changed or new files. Keep `--paths` on
directories for full builds. PHPStan disables its result cache when it is handed
individual files, so a file-list scope pays the full cost every single time.

If a refreshed map must feed a later approval or Recall recompile, rebuild the
Search index first:

```bash
tools/agent-loop/vendor/bin/agent-loop map search-index build
```

The map output (`agent-loop init paths` reports `map_root`) is generated
navigation state. Confirm it is ignored; never force-add the index. `workflow
context` reads existing generated evidence but never builds a map or Search
index itself.

## Validation After Start

```bash
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id>
tools/agent-loop/vendor/bin/agent-loop verify
```

`workflow status` confirms the Session, Run, Recall, Contract, and approval state.
`verify` confirms cross-package consistency from the start.

## Lower-Level Fallback

Use this only when you intentionally need direct control over Session and Recall
outside the governed PLAN/APPROVE path:

```bash
tools/agent-loop/vendor/bin/agent-loop session start --task <task-id> --by <actor> --base-commit "$(git rev-parse HEAD)"
tools/agent-loop/vendor/bin/agent-loop recall compile \
  --task <task-id> \
  --file <path-to-file-1> \
  --file <path-to-file-2>
```

`session start` prints a date-prefixed session id on its first line. The Loop
`recall compile` wrapper resolves the configured Learning and Recall roots; do
not hardcode them. Inspect the project layout when you need the physical paths:

```bash
tools/agent-loop/vendor/bin/agent-loop init paths --format=json
```

Without an explicit output override, Recall artifacts live below the configured
`<recall-root>/<task-id>/`.

## Recall Output Is Not Auto-Injected

`recall compile` writes deterministic artifacts such as `system.md`,
`validation-plan.md`, `recall-log.draft.json`, and `meta.json` below the configured
`<recall-root>/<task-id>/`. These artifacts are not automatically passed into a
coding agent by the standalone compiler. The governed Loop workflow and any
external harness must explicitly consume them.

## Skill Boundary

This skill owns:

- the opening step of a governed agent-loop task in a consuming repository
- choosing a task id, actor, file scope, non-goals, explicit acceptance criteria, behavior anchors, and validation commands
- checking bounded prior/parallel work when the host exposes relevant history, and falsifying the strongest existing candidate before creating a competing implementation
- establishing map and Search readiness before approval when ranked map evidence is expected
- understanding that `workflow plan` creates/revises a candidate Contract and `workflow approve` creates the governed working state and compiles Recall from its approved revision
- obtaining human approval before implementation and inspecting the bounded context
- inspecting initial state with `workflow status` and `verify`

This skill does not own:

- the review and close steps (see `agent-loop-review-close`)
- L2 context recompilation during a task (see `agent-loop-l2-context`)
- developing `agent-loop` itself

## Example Triggers

- "Start an agent-loop task for this change."
- "Compile context for this repo before editing."
- "Use agent-loop for this task."
