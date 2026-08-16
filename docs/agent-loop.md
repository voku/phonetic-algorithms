# agent-loop in this repository

This repository uses [`voku/agent-loop`](https://github.com/voku/agent-loop) as
its coding-agent workflow: a task is planned, approved, implemented, validated
with recorded evidence, reviewed and only then closed.

## Why an isolated tool project

`voku/agent-loop` requires PHP 8.3+, but `voku/phonetic-algorithms` still
supports PHP 7.x and its CI matrix runs `composer update` from PHP 7.1 upwards.
Putting agent-loop into the root `require-dev` would make that install
unsolvable on every old PHP version.

It therefore lives in its own Composer project:

```text
tools/agent-loop/composer.json      # pins the tested agent-loop release
tools/agent-loop/composer.lock      # pins the complete isolated tool graph
tools/agent-loop/vendor/bin/...     # not tracked
bin/agent-loop                      # wrapper used by the Makefile and the agent
```

Install it once:

```bash
make agent_loop_install       # composer install --working-dir=tools/agent-loop
bin/agent-loop init status
```

`init status` prints the exact activation commands for this checkout. Host
assets such as `.claude/` are generated from the pinned Composer package and are
**not tracked**; this avoids keeping a second, stale copy of package-owned
workflow guidance in the repository. Generate or refresh them locally with:

```bash
make install_agent_assets     # or: bin/agent-loop init install-assets --agent=claude
```

The tracked `AGENTS.md` and `CLAUDE.md` files remain the small repository
routers, so a clean clone can discover how to install and activate the tooling
without committing generated host projections.

## Workflow state

| Path | Tracked | What it is |
| --- | --- | --- |
| `.agent-loop/todo/` | yes | the Kanban board and its cards |
| `.agent-loop/tasks/` | yes | the task definition the verifier reads |
| `.agent-loop/contracts/` | yes | the approved scope/validation contract per task |
| `.agent-loop/runs/` | yes | durable Run identity, close and verification receipts |
| `.agent-loop/learning/` | yes | validated findings and durable Run-learning decisions |
| `.agent-loop/map/` | no | agent-map navigation index, disposable |
| `.agent-loop/recall/` | no | compiled briefings and review prompts, regenerable |
| `.agent-loop/sessions/` | no | pruneable working memory of a run |

The durability split is intentional: pruning Session state must not erase the
approved Contract, governed Run identity, final verification receipts, or
Learning close-out needed for later audit.

## The board

```bash
bin/agent-loop board summary
bin/agent-loop board render
bin/agent-loop board card show PHON-3
```

Lanes are `BACKLOG -> READY -> DOING -> VERIFY`, with `BLOCKED` as a side lane.
A card needs an **Agent Task Brief** before it may enter `READY`.

## One governed task, end to end

Use the installed package-owned skills for the current detailed command
sequence. The tracked router deliberately does not copy that procedure. The
stable high-level path is:

```text
PLAN -> APPROVE -> CONTEXT -> IMPLEMENT -> VALIDATE -> REVIEW -> LEARN -> CLOSE
```

Before mutating a durable task, inspect its current persisted status. Build the
map and search index before approval when ranked map evidence is expected. Run
the exact validation obligations from the approved Contract, preserve observed
results, and close only when the current Run gates pass.

## Findings about the workflow itself

Observations about agent-loop are **not** fixed inside a product task. They are
collected as normal validated Learning findings with the affected external
package identified, so they can be handed back to the owning agent-* repository
without maintaining a second prose feedback lifecycle.
