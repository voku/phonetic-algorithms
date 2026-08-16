---
name: agent-loop-review-close
description: Review, verify, and close an agent-loop task safely after implementation, including blind-spot review, strict verification, truthful Recall outcomes, governed Run learning close-out, accepted-risk boundaries, and optional reflection.
---

# Agent Loop Review Close

Use this skill when implementation is done or nearly done and the governed Run
needs evidence, review, learning close-out, verification, and final close.

## Fast Path

Resolve project-owned paths first when a physical artifact path is needed:

```bash
tools/agent-loop/vendor/bin/agent-loop init paths --format=json
```

Then record real execution evidence, review, Recall outcomes, and the Run learning
decision before verify/report/close:

```bash
tools/agent-loop/vendor/bin/agent-loop session validation record <task-id> --contract-revision <n> --command "<Contract command>" --status passed --exit-code 0 --by <actor>
tools/agent-loop/vendor/bin/agent-loop review blindspots <task-id>
tools/agent-loop/vendor/bin/agent-loop recall log-outcome --draft <recall-root>/<task-id>/recall-log.draft.json --by <actor> --commit <sha>
tools/agent-loop/vendor/bin/agent-loop workflow learn <task-id> --status no_durable_learning --by <actor> --reason "No reusable finding from this bounded task."
tools/agent-loop/vendor/bin/agent-loop verify --task-id=<task-id>
tools/agent-loop/vendor/bin/agent-loop workflow report <task-id> --changed-file <path>

# Optional at ready_to_close when extra task scrutiny is useful:
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope task

tools/agent-loop/vendor/bin/agent-loop workflow close <task-id> --status done
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id> --expect complete

# Optional after close when the work exposed a meaningful future investment:
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope project
```

Do not invent passing evidence simply to satisfy the order. Reflection is not one
more completion gate.

## Blind-Spot Review

```bash
tools/agent-loop/vendor/bin/agent-loop review blindspots <task-id>
```

This deterministic Recall review writes Markdown/JSON reports below the
configured `<recall-root>/<task-id>/reviews/`. It uses current task/Run/Session
and Recall artifacts; the report is evidence for review, not human approval and
not durable-learning approval.

## Validation Evidence

The Contract owns the required validation command strings. A close only accepts
passing evidence recorded against the **current Contract revision** and the exact
command text:

```bash
tools/agent-loop/vendor/bin/agent-loop session validation record <task-id> \
  --contract-revision <n> \
  --command "<exact Contract validation command>" \
  --status passed \
  --exit-code 0 \
  --by <actor>
```

Record a pass only after observing the command result. Re-planning creates a new
Contract revision; stale evidence for an older revision does not satisfy it.

## Verify

Task-scoped verification:

```bash
tools/agent-loop/vendor/bin/agent-loop verify --task-id=<task-id>
```

Repository-wide verification remains available as:

```bash
tools/agent-loop/vendor/bin/agent-loop verify
```

Use `--strict` where all expected roots/components must exist rather than being
allowed to skip. `agent-loop init paths --format=json` is the authority for
configured project roots.

## Optional Reflection

Reflection is read-only and available only when the governed Run is
`ready_to_close` or `complete`.

Task reflection asks whether extra scrutiny exposes a real missed completion
requirement:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope task
```

If it returns `RETURN_TO_REVIEW`, do not close. Route the concrete gap back
through REVIEW/IMPLEMENT/PLAN as appropriate. Otherwise the suggested deepening
remains optional.

Project reflection asks what future investment became visible through the work:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope project
```

Use it after successful close when worthwhile. It does not automatically create
issues, findings, durable guidance, or follow-up work.

## Close

```bash
tools/agent-loop/vendor/bin/agent-loop workflow close <task-id> --status done
```

Before ordinary successful close, the code requires at least:

- the current durable Task Contract is approved;
- the governed Run is bound to that exact Contract revision;
- any selected L2 execution contract is current and `ready` (or not required);
- Recall metadata exists;
- blind-spot review exists and is not failing;
- each Contract validation obligation has passing current-revision evidence;
- selected Recall guidance/constraints have explicit outcomes when required;
- the durable Run learning conclusion exists;
- an existing edit bundle has passed `agent-loop edit verify`;
- task verification passes.

`workflow report` is the read-only handoff view. Pass observed changed files with
repeatable `--changed-file`; it deliberately does not run Git or infer scope.

## Accepted Risk

Accepted risk is a named waiver for the **bypassable evidence gates**, not a
universal “make it green” switch:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow close <task-id> \
  --status done \
  --accept-risk "<specific understood risk>" \
  --accept-risk-by "<named actor>"
```

Both values are required. The resulting record is written below the configured
`<risks-root>/` and names the actor, reason, and gates that were failing at the
time of close.

Two authority gates are explicitly **not bypassable** by accepted risk:

1. the governed Run must still be bound to the current approved Contract revision;
2. when L2 policy is selected, the execution contract must still be current and
   `ready` (`not_required` is valid when no L2 contract is required).

If either fails, re-plan/re-approve or repair the execution contract. Do not use
accepted risk to change what was approved.

## Edit Bundle Boundary

If an edit bundle exists below the configured `<edit-root>/<task-id>/`, close
requires its `verification-result.json` to report `passed`. A task that never ran
`agent-loop edit` has no edit bundle and is not required to invent one. A bundle
that exists but was never successfully verified blocks ordinary close and is
recorded explicitly if a named actor accepts that bypassable risk.

## When Verification Fails

1. Read the exact failing gate and evidence reference.
2. Fix the underlying issue: re-plan/approve changed scope, satisfy the execution
   contract, run and record the missing validation, log Recall outcomes, record
   the Run learning decision, verify an edit bundle, or repair Session/Run state.
3. Record a concise checkpoint when the resolution matters for resumability:
   ```bash
   tools/agent-loop/vendor/bin/agent-loop session checkpoint <task-id> --title "Verify fix" --body "..."
   ```
4. Re-run task verification.
5. Use accepted risk only for a remaining bypassable gate whose concrete risk is
   understood and explicitly owned.

## Close Is Not Durable Learning Approval

`workflow close` consumes the Run learning decision but does not approve durable
guidance. Findings/proposals remain governed learning inputs. See the installed
`agent-loop-learning-boundary` skill.

Reflection is separate again: it neither approves learning nor becomes learning
merely because it produced an interesting idea.

## Validation

Before claiming completion:

- blind-spot review exists below `<recall-root>/<task-id>/reviews/` and is not failing;
- every required validation obligation has observed passing evidence for the current Contract revision;
- selected Recall guidance has explicit truthful outcomes;
- an explicit durable **Run learning decision** exists;
- `tools/agent-loop/vendor/bin/agent-loop verify --task-id=<task-id>` passes, unless a named actor explicitly accepts only bypassable remaining gates;
- `workflow report` shows no unaccepted scope/evidence gap;
- any task reflection `RETURN_TO_REVIEW` is resolved;
- `workflow close <task-id> --status done` succeeds;
- `workflow status <task-id> --expect complete` succeeds.

## Skill Boundary

This skill owns review/evidence/final-close mechanics. It does not own task
planning (`agent-loop-task-start`), Recall-specific command semantics
(`agent-recall-consumer`), or durable-learning promotion
(`agent-loop-learning-boundary`).

## Example Triggers

- "Close this agent-loop task."
- "Run the review/verify gate."
- "Can I mark this done?"
- "What did we miss before closing?"
- "Accept this specific risk and close."
