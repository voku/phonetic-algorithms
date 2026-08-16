# agent-loop in this repository

This repository uses [`voku/agent-loop`](https://github.com/voku/agent-loop) as
its coding-agent workflow: a task is planned, approved, implemented, validated
with recorded evidence, reviewed and only then closed.

## Why an isolated tool project

`voku/agent-loop` requires PHP 8.3+, but `voku/phonetic-algorithms` still
supports PHP 7.x and its CI matrix runs `composer update` from PHP 7.1 upwards.
Putting agent-loop into the root `require-dev` would make that install
unsolvable on every old PHP version.

It therefore lives in its own Composer project, the same pattern agent-loop
itself uses for `slop-scan`:

```text
tools/agent-loop/composer.json      # requires voku/agent-loop
tools/agent-loop/vendor/bin/...     # not tracked
bin/agent-loop                      # wrapper used by the Makefile and the agent
```

Install it once:

```bash
make agent_loop_install       # composer install --working-dir=tools/agent-loop
bin/agent-loop init status
```

`init status` prints the exact activation commands for this checkout. The
projected Claude Code assets under `.claude/` are generated from the Composer
package and are refreshed with:

```bash
make install_agent_assets     # or: bin/agent-loop init install-assets --agent=claude
```

## Workflow state

| Path | Tracked | What it is |
| --- | --- | --- |
| `.agent-loop/todo/` | yes | the Kanban board and its cards |
| `.agent-loop/tasks/` | yes | the task definition the verifier reads |
| `.agent-loop/contracts/` | yes | the approved scope/validation contract per task |
| `.agent-loop/runs/` | yes | the durable close and verification receipts |
| `.agent-loop/learning/` | yes | validated findings |
| `.agent-loop/map/` | no | agent-map navigation index, disposable |
| `.agent-loop/recall/` | no | compiled briefings and review prompts, regenerable |
| `.agent-loop/sessions/` | no | pruneable working memory of a run |

## The board

```bash
bin/agent-loop board summary
bin/agent-loop board render
bin/agent-loop board card show PHON-3
```

Lanes are `BACKLOG -> READY -> DOING -> VERIFY`, with `BLOCKED` as a side lane.
A card needs an **Agent Task Brief** before it may enter `READY`.

## One governed task, end to end

```bash
bin/agent-loop board card move PHON-3 --to=DOING --actor=<name>

bin/agent-loop map refresh
bin/agent-loop map search-index build

bin/agent-loop workflow plan PHON-3 \
  --by <name> \
  --file src/voku/helper/PhoneticSpanish.php \
  --goal '...' \
  --validation 'php vendor/bin/phpunit -c phpunit.xml'

bin/agent-loop workflow approve PHON-3 --by <name>
# -> read .agent-loop/recall/PHON-3/system.md before touching code

# ... implement ...

php vendor/bin/phpunit -c phpunit.xml
bin/agent-loop session validation record PHON-3 \
  --contract-revision 1 \
  --command 'php vendor/bin/phpunit -c phpunit.xml' \
  --status passed --exit-code 0 --by <name>

bin/agent-loop review blindspots PHON-3
bin/agent-loop session checkpoint PHON-3 --title 'review blindspots PHON-3' --body '...'

bin/agent-loop workflow learn PHON-3 --status no_durable_learning --by <name> --reason '...'
bin/agent-loop verify --task-id=PHON-3
bin/agent-loop workflow close PHON-3 --status done
bin/agent-loop workflow status PHON-3 --expect complete
```

`workflow approve` refuses to run while the agent-map snapshot is older than the
PHP files in scope, so `map refresh` belongs *before* approval.

## Findings about the workflow itself

Observations about agent-loop are **not** fixed inside a product task. They are
collected on the meta card `PHON-9` and written up in
[`docs/agent-loop-dogfood.md`](agent-loop-dogfood.md).
