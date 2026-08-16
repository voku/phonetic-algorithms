---
name: agent-loop-workflow
description: Use the governed agent-loop state machine for planning, approval, bounded context, L2 execution contracts, implementation evidence, review, learning decisions, verification, safe closure, and optional post-task reflection.
---

# Agent Loop Workflow

Use this skill when operating or changing a task under the governed `agent-loop`
workflow. Apply `agent-loop-discipline` throughout implementation and
`agent-loop-simplify-review` as a separate complexity-only pass when the diff may
contain speculative code.

Persisted workflow artifacts are the execution state. Conversation prose is not.
Start by resolving the existing task/session state instead of inventing another
plan beside it.

## Deterministic Phase Model

```text
PLAN -> APPROVE -> CONTEXT -> CONTRACT -> IMPLEMENT -> VALIDATE -> REVIEW -> LEARN -> CLOSE
```

Reflection is deliberately **not** another lifecycle phase. It is a read-only
reasoning surface available only once normal completion evidence reaches
`ready_to_close` or `complete`.

| Phase | Required evidence before leaving | Route |
|---|---|---|
| `PLAN` | candidate Contract with goal, scope, non-goals, behavior anchors, validation, and selected operating-prompt policy when used | `agent-loop-task-start` |
| `APPROVE` | named human approval of that exact Contract revision | human gate |
| `CONTEXT` | bounded approved recall plus verified real-source locations | `agent-loop-l2-context`, then `agent-loop-investigate` when location is unknown |
| `CONTRACT` | for selected L2 recipes, one current project-specific L1 bound to the approved Contract + recall bundle | `workflow contract` |
| `IMPLEMENT` | smallest correct diff inside approved scope; required execution contract is current | `agent-loop-surgical-edit` for verified 1-2 file scope; otherwise main workflow |
| `VALIDATE` | exact required commands recorded against current Contract revision and implementation snapshot | `agent-loop-task-progress` |
| `REVIEW` | blind-spot artifact bound to the current implementation plus complete raw-diff correctness review; complexity pass when relevant | `agent-loop-code-review`, `agent-loop-simplify-review` |
| `LEARN` | truthful recall/recipe outcomes plus explicit learning decision bound to current post-execution evidence | `agent-loop-learning-boundary` |
| `CLOSE` | authoritative final consistency check accepts current evidence and any required L2 execution contract is still current | `agent-loop-review-close` |

Transitions are evidence-driven, not optimistic:

- scope or task policy exceeds the approved Contract -> `PLAN` and obtain approval again;
- approval/recompile changes the recall bundle -> any prior execution contract becomes superseded/stale -> `CONTRACT`;
- required L2 contract is missing, stale, or invalid -> `CONTRACT`, never `IMPLEMENT`;
- contract construction proves the approved task cannot be executed safely -> record `BLOCKED` or `REJECTED`; do not weaken policy silently;
- validation fails because implementation is wrong -> `IMPLEMENT`;
- validation exposes missing scope or product intent -> `PLAN`;
- implementation changes after validation or review -> repeat the affected evidence for the new implementation snapshot;
- correctness review finds a defect -> `IMPLEMENT`, then repeat validation/review;
- task reflection returns `RETURN_TO_REVIEW` -> the task was not actually complete; route the concrete gap back through REVIEW/IMPLEMENT/PLAN as appropriate;
- a reusable finding exists -> remain in `LEARN` until it is recorded truthfully;
- a proposal is never self-approved by an agent;
- failed final verification -> repair the missing gate, do not claim `CLOSE`;
- accepted risk is an explicit named human override for bypassable close gates; it never bypasses implementation-evidence integrity or a required L2 execution contract.

## Fast Path

1. Inspect prior history only when earlier decisions materially affect the task.
2. Resolve existing task/session state and reuse the stable task id.
3. Plan explicit goal, scope, non-goals, behavior anchors, exact validation, and any selected operating-prompt recipe + explicit arguments.
4. When PHP scope or ranked map evidence applies, build or refresh the semantic map and build its separate Search index before approval.
5. Approve that exact revision through a named human actor.
6. Compile/use bounded recall; use `agent-map` to select precise source reads.
7. When recall contains L2 recipes, follow the current Recall-owned construction instructions and persist exactly one project-specific L1 with `workflow contract` before mutation.
8. Implement the smallest correct change in the owning package.
9. Record validation against the current Contract revision and implementation snapshot.
10. Review blind spots, the complete raw diff, and complexity separately when needed.
11. Record recall outcomes, evidence-backed operating-prompt outcomes, and an explicit learning decision.
12. Optionally use `agent-loop verify --task-id=<task-id>` and `workflow report` for diagnostics or CI; they are not a second mandatory happy-path gate.
13. At `ready_to_close`, optionally run task reflection when extra scrutiny is useful; `RETURN_TO_REVIEW` routes back instead of closing.
14. Close. `workflow close --status done` performs the authoritative final cross-package consistency check itself and re-projects the Run before returning success.
15. After successful close, optionally run project reflection and report one highest-leverage future investment or `nothing worthwhile`; never create follow-up work automatically.

Do not ask the human to run reads, edits, tests, or reports that the available
tools can run. Human interaction is reserved for approval, genuine ambiguity,
irreversible actions, and explicit risk ownership.

## Canonical Flow

Without an L2 recipe:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow plan <task-id> \
  --by <actor> \
  --file <path> \
  --goal "Implement the approved task." \
  --behavior-anchor "request -> service -> persisted state" \
  --validation "vendor/bin/phpunit tests/FocusedTest.php"

tools/agent-loop/vendor/bin/agent-loop map build --paths=src,tests
tools/agent-loop/vendor/bin/agent-loop map search-index build
tools/agent-loop/vendor/bin/agent-loop workflow approve <task-id> --by <human-actor>
```

With a reusable L2 recipe, selection is part of the Contract that gets approved.
Use the catalog shipped by the tool that owns those recipe semantics:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow plan <task-id> \
  --by <actor> \
  --file <path> \
  --goal "Harden the parser tests." \
  --validation "composer ci" \
  --operating-prompt-manifest vendor/voku/agent-recall-compiler/skills/agent-recall-consumer/operating-prompts.json \
  --operating-prompt '{"id":"coverage-mutation","arguments":{"minimum_percentage_points":10,"mutation_command":"vendor/bin/infection"}}'

tools/agent-loop/vendor/bin/agent-loop map build --paths=src,tests
tools/agent-loop/vendor/bin/agent-loop map search-index build
tools/agent-loop/vendor/bin/agent-loop workflow approve <task-id> --by <human-actor>
tools/agent-loop/vendor/bin/agent-loop workflow context <task-id> --max-lines 120 --max-bytes 12000
```

`map build` and `map search-index build` are separate operations. Approval compiles
governed Recall immediately, so build the Search index first when ranked map
evidence is expected. Resolve configurable map paths through `agent-loop init
paths`; do not assume a physical state directory.

Recall owns the reusable recipe schema and L2 construction semantics. Loop owns
selection persistence in the Contract, approval, execution-contract binding, and
lifecycle progression. Do not keep a second canonical copy of Recall recipe
instructions in this skill.

## Built-in L1 Control Prompts

`agent-loop` also ships context-independent controls in
`resources/operating-prompts.json`. Select them through the same approved
operating-prompt policy rather than inventing another control path.

Checkpoint autonomy requires an explicit anchor supplied by the caller:

```bash
--operating-prompt-manifest vendor/voku/agent-loop/resources/operating-prompts.json \
--operating-prompt '{"id":"checkpoint-autonomy","arguments":{"anchor_point":"each independently verifiable repository step"}}'
```

At each anchor, inspect current scope, evidence, validation, blockers, and the
done condition. If the checkpoint passes and no real human-only gate exists,
record a concise session checkpoint and continue automatically. Never fabricate
a human/self approval record.

Momentum reuses still-valid current understanding instead of restarting discovery:

```bash
--operating-prompt '{"id":"momentum","arguments":{}}'
```

Reuse files, symbols, commands, constraints, decisions, and evidence aggressively;
revalidate authority, freshness, repository scope, and assumptions mechanically.
Both built-in controls are L1-only and do not require an L2 construction pass by
themselves. A separately selected L2 engineering recipe keeps its normal contract
gate.

When Recall requires an L2 construction pass, follow the current construction
contract rendered in the task's Recall artifacts. Persist that generated contract
before implementation:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow contract <task-id> \
  --status ready \
  --from <project-specific-l1.md> \
  --by <actor>
```

If construction proves the approved contract cannot safely be executed, persist the stop state instead of weakening it:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow contract <task-id> \
  --status blocked \
  --reason "<blocking requirement>" \
  --evidence "<observable evidence>" \
  --affected-constraint "<constraint when known>" \
  --minimum-change "<smallest approved contract change that would unblock>" \
  --by <actor>
```

`REJECTED` is used when an implementation/approach violated the approved contract and must be discarded/reconstructed rather than repaired by negotiating around the violation.

Continue source navigation after the contract is current. Refresh generated
navigation state when source changed rather than pretending the pre-approval
snapshot is eternal:

```bash
tools/agent-loop/vendor/bin/agent-loop map refresh
tools/agent-loop/vendor/bin/agent-loop map query <symbol>
tools/agent-loop/vendor/bin/agent-loop map related <symbol>
```

## Execution Contract Boundary

The persisted L1 is stored beside the task recall artifacts as:

```text
<recall-root>/<task-id>/execution-contract.md
<recall-root>/<task-id>/execution-contract.json
```

The metadata binds the document to the current:

- task id;
- Contract revision;
- recall bundle digest;
- selected L2 prompt semantics/arguments;
- content digest and actor.

Re-planning and approving a newer brief supersedes the prior recall task directory, so an old execution contract cannot silently remain current. `workflow status` and `workflow manifest` expose `execution_contract` as `missing`, `ready`, `stale`, `blocked`, `rejected`, or `invalid` when the gate applies.

For an active governed task, mutating `agent-loop edit` runners (`command`, `mechanical`, `auto`) require the current L2 execution contract to be `ready`. Read-only prompt/context preparation remains possible before that gate. Context-independent L1-only recipes do not require a synthetic L2 construction pass.

## Workflow Boundary

- Planning records a candidate Contract; approval seals its exact revision, including selected operating-prompt manifest/recipes/arguments.
- Re-planning invalidates approval and validation evidence for the old revision.
- Validation, review, and Run Learning evidence are bound to the implementation snapshot they describe; later implementation content cannot reuse earlier evidence as current.
- `workflow context`, `status`, `report`, and `reflect` are read-only.
- `workflow context` never rebuilds recall or a map.
- `workflow report` does not run Git; pass observed changed paths explicitly.
- `agent-loop verify --task-id=<task-id>` remains available for diagnostics, CI, and pre-close inspection; it is optional in the normal happy path.
- `workflow reflect` emits a deterministic context-light prompt only when the run is `ready_to_close` or `complete`; it does not call a model or mutate workflow state.
- `workflow close --status done` is the authoritative final consistency check: it requires the current approval, current implementation-bound validation/review/Learning evidence, recall outcomes, passing cross-package verification, and any required current L2 execution contract; after writing the final projection it re-projects and requires `complete` before success.
- `--accept-risk` may override only explicitly bypassable close gates; it never bypasses stale implementation evidence or a required L2 execution contract.
- Recall files are not silently injected into an agent.
- Findings are not durable memory until reviewed and promoted.
- One task has one active session; resume it rather than creating parallel state.

## Optional Reflection

Reflection answers a different question from REVIEW and LEARN:

```text
REVIEW             = Is this task actually complete/correct?
LEARN              = What observed knowledge should potentially survive this task?
TASK REFLECTION    = With more time in this completed task, what extra depth or missed opportunity matters?
PROJECT REFLECTION = What future investment became visible through doing this work?
```

Task reflection is most useful at `ready_to_close`:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope task
```

If it returns `RETURN_TO_REVIEW`, treat that as evidence that the completion bar
was false and route the concrete gap back through the existing lifecycle before
close. Otherwise the suggested deepening is optional.

After successful close, project reflection may surface one future investment:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope project
```

Do not turn reflection into a mandatory gate, durable learning approval, or an
automatic issue/backlog generator. `nothing worthwhile` is a valid result.

The L2 briefing labels claims `VERIFIED`, `INFERRED`, `ASSUMED`, `BLOCKED`, or
`CONTRADICTED`. Model explanations and review comments remain hypotheses until
current repository evidence, focused history, or a safe runtime observation
supports them.

## Project Capability Evidence

Recall may expose bounded `project.capabilities` evidence from the configured project root: exact Composer scripts, runtime constraint, known test/static-analysis/mutation/formatting tool packages and configs, plus CI workflow anchors. Treat package presence as evidence that a tool exists, **not** proof of a command. Exact Verification commands must come from repository-supported scripts, task validation, constraints, or other explicit evidence. Missing commands remain `UNKNOWN` and become discovery work; do not fabricate them.

## Progress Receipt

After a meaningful phase transition, result, or blocker, report the compact
contract from `agent-loop-discipline`:

```text
RESULT: <verified result>
STATE: <phase> <task-id> <Contract revision when known>
NEXT: <one agent-owned action or exact human gate>
```

Do not narrate every tool call. Do not repeat unchanged state. Derive `STATE`
from persisted artifacts or observed command results, never from intention.

## Navigation And Evidence

Generated map files are disposable navigation state. Resolve the configured
`map_root`, query them through the CLI, then inspect the selected real source.
Do not dump map databases into a prompt.

Keep complete and unchanged:

- source files;
- full diffs and per-file patches;
- test and static-analysis output;
- generated verification artifacts;
- execution-contract metadata/document;
- redirected harness files and decisive errors.

Concise summaries help humans navigate evidence; they never replace it. Run
repository commands normally. Do not add a command or output rewriter merely to
make evidence shorter.

## Historical Evidence

```bash
ctx search "<task / failure / module / command>"
ctx show event <ctx-event-id> --window 5
```

Inspect focused hits before using them. Persist only bounded IDs, query, reviewed
summary, retrieval time, and verification status. Never promote raw transcripts
or unverified history.

## Validation Evidence

```bash
tools/agent-loop/vendor/bin/agent-loop session validation record <task-id> \
  --contract-revision <current-revision> \
  --command "vendor/bin/phpunit tests/FocusedTest.php" \
  --status passed \
  --exit-code 0 \
  --by <actor>
```

The implementation snapshot is derived by the governed dispatcher. Do not pass a
caller-invented snapshot identity. Never infer a pass from missing output, an
agent summary, or an earlier brief.

## Learning And Recipe Outcomes

`recall-log.draft.json` contains ordinary guidance outcomes and, when prompt recipes were selected, `operating_prompt_outcomes`. Final `helpful`, `irrelevant`, or `harmful` recipe classifications require concrete evidence; helpful/harmful additionally require the recipe to have been applied. Record the outcome before final logging. Prior aggregate recipe outcomes are future recall evidence, not authority to rewrite the recipe automatically.

A learning decision records an outcome; it does not approve durable guidance or mutate reusable prompt semantics.

## Review And Close

```bash
tools/agent-loop/vendor/bin/agent-loop review blindspots <task-id>

tools/agent-loop/vendor/bin/agent-loop workflow learn <task-id> \
  --status no_durable_learning \
  --by <actor> \
  --reason "No reusable finding from this bounded task."

# Optional diagnostics / CI. CLOSE runs the final verifier itself.
tools/agent-loop/vendor/bin/agent-loop verify --task-id=<task-id>
tools/agent-loop/vendor/bin/agent-loop workflow report <task-id>

# Optional before close when deeper scrutiny is useful:
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope task

tools/agent-loop/vendor/bin/agent-loop workflow close <task-id> --status done
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id> --expect complete

# Optional after close when the work exposed a meaningful future investment:
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope project
```

## Guidance Changes

When package-owned agent behavior changes, run:

```bash
tools/agent-loop/vendor/bin/agent-loop init validate --kind=all
tools/agent-loop/vendor/bin/agent-loop init install-assets --agent=all --dry-run
tools/agent-loop/vendor/bin/agent-loop init doctor
composer dogfood:discipline
vendor/bin/phpunit --filter 'AgentDisciplineHook|InitInstallAssets|Init|DispatcherTest'
vendor/bin/phpstan analyse --configuration=phpstan.neon.dist --memory-limit=512M
composer ci
```

Claim only checks whose exit status was observed.
