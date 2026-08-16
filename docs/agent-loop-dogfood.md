# Dogfooding `voku/agent-loop` in `voku/phonetic-algorithms`

**Date:** 2026-08-16
**Version under test:** `voku/agent-loop` 0.16.4 (installed from Composer into
`tools/agent-loop`)
**Scope:** the eight governed runs PHON-1 … PHON-8 - validating the three
shipped phonetic classes and adding five new ones.

This is the report the meta card **PHON-9** exists for. It records what the
workflow caught, what it missed, and what was awkward, with the exact command
output or source line for each point. Nothing here was fixed inside
`voku/agent-loop`; that is deliberately out of scope for a product task.

Every entry is labelled **VERIFIED** (reproduced from output or source),
**INFERRED** (derived, not observed) or **ASSUMED**.

---

## 1. What the workflow caught

### 1.1 The map-freshness gate stopped a real stale briefing — VERIFIED

`workflow approve` refused to run for PHON-2:

```text
[FAIL] workflow approve: Existing PHP scope is not covered by a fresh agent-map
snapshot before approval (stale map entries: src/voku/helper/PhoneticGerman.php,
tests/EnglishPhoneticAlgorithmsTest.php, tests/FrenchPhoneticAlgorithmsTest.php,
tests/GermanPhoneticAlgorithmsTest.php).
```

Those four files had really been changed by PHON-1. Without the gate the PHON-2
briefing would have been compiled from a pre-PHON-1 index. This is the single
most valuable check in the whole loop, because it fails *before* the artifact
that everything downstream trusts is written.

### 1.2 Forcing a written Contract before implementation changed the work — VERIFIED

`workflow plan` demands `--goal`, `--acceptance`, `--non-goal` and `--validation`
before any code is touched. In PHON-1 the acceptance criterion "*every Kölner
Phonetik code-table row is covered by an explicit assertion*" is what made the
contradiction inside the class docblock visible - two rows of the table
disagreed about `C` after `R`, and the code followed the wrong one. The bug had
been in the shipped library and in its test-suite (`'Kirche' => '478'`) for
years. It was found by reading the table row by row because the Contract said so,
not by reading the code.

### 1.3 `workflow status --expect complete` is a real CI gate — VERIFIED

It exits non-zero unless board, session, contract, approval, review, learning
and verification all line up. That is assertable from CI, unlike an agent
reporting "done".

### 1.4 The blind-spot *prompt* is worth more than the blind-spot *check* — VERIFIED

The deterministic check only looks for marker strings (see 2.1). The generated
`*.blindspots.prompt.md`, read as a checklist, is what produced the useful
outcomes: in PHON-1 it is why the behaviour change got a `CHANGELOG` entry
saying that an existing phonetic index has to be rebuilt.

---

## 2. What the workflow missed or reported wrongly

### 2.1 The blind-spot review gate is satisfied by self-declaration — VERIFIED

`voku/agent-recall-compiler/src/Review/BlindSpotReviewer.php:19`

```php
private const array REVIEW_MARKERS = ['review blindspots', 'review-blindspots', 'L2 blind-spot'];
```

The check greps the recall artifacts and the session files for one of those
strings. Writing a session checkpoint **titled** `review blindspots PHON-1`
flips the gate from `warn` to `ok`. Nothing verifies that the prompt was read,
that the review produced findings, or that the checkpoint says anything.

*Suggestion for agent-loop:* bind the checkpoint to the report it claims to
answer - for example require the checkpoint body to contain the
`bundle_sha256` of the blind-spot report, which cannot be written before the
report exists.

### 2.2 `board summary` can never report a finished card — VERIFIED

After archiving seven cards:

```text
$ ls .agent-loop/todo/archive | wc -l
7
$ bin/agent-loop board summary
_Total: 2 active card(s); 0 done/archived. Format version: 1._
```

`BoardRenderer.php:55` prints `$summary->doneCount`, which comes from
`BoardMetadata.php:42`:

```php
(int) ($metadata['Done count'] ?? 0),
```

`Done count` is a field of `todo/board.md`. Grepping the package shows the field
has **no writer**: it is read there and in fixtures only, and
`CardMutationService::archive()` moves the card file without touching board
metadata. On top of that, `init scaffold` does not write the field into
`board.md` at all, so the default `?? 0` is what is always reported.

*Suggestion for agent-loop:* either count the files in the archive directory or
drop the counter from the rendered summary. A number that is structurally always
zero is worse than no number.

### 2.3 There is no command that creates a finding — VERIFIED

The documented close-out is
`workflow learn <task> --status findings_recorded --finding <id>`, and
`learn finding-id` allocates an id:

```text
$ bin/agent-loop learn finding-id
finding.2026-08-16.21d27d
```

… but nothing writes the finding file. `learn help` lists `validate`, `prepare`,
`finding-id`, `finding-transition`, `backlog`, the proposal commands - no
`finding-create`. Both findings in this repository were hand-written as JSON
against the schema copied from
`vendor/voku/agent-learning/examples/guidance-evaluation/findings/validated/*.json`.

`init scaffold` also creates `learning/findings/` but not
`learning/findings/validated/` (`InitScaffoldCommand.php:63`), so the directory
that `learn validate` reads has to be created by hand.

*Suggestion for agent-loop:* a `learn finding-create --task --observation
--conclusion --scope` that allocates the id and writes the file would remove
both papercuts, and would stop each project from inventing its own JSON.

### 2.4 The quick-start has no path for a host repository with an older PHP floor — VERIFIED

`voku/agent-loop` requires PHP 8.3+. This library supports PHP 7.x and its CI
matrix runs `composer update` from PHP 7.1 upwards, so putting agent-loop into
the root `require-dev` makes the install unsolvable on most of the matrix. The
docs only ever say `vendor/bin/agent-loop`.

The pattern that works is the one agent-loop already uses for `slop-scan`: an
isolated tool project (`tools/agent-loop/composer.json`) plus a wrapper script.
`init status` resolved the non-standard path by itself and printed
`[OK] CLI: bin/agent-loop`, so the tooling supports it - only the documentation
does not mention it.

*Suggestion for agent-loop:* document the isolated tool project as the supported
way to add the loop to a library that cannot raise its PHP floor.

### 2.5 The compiled briefing repeats one path four times — VERIFIED

`.agent-loop/recall/PHON-1/system.md`:

```text
## Navigation Facts
- /home/user/phonetic-algorithms/.agent-loop/map/php-symbols.json
- /home/user/phonetic-algorithms/.agent-loop/map/php-symbols.json
- /home/user/phonetic-algorithms/.agent-loop/map/php-symbols.json
- /home/user/phonetic-algorithms/.agent-loop/map/php-symbols.json
```

One line per task file, all four pointing at the same index. Pure noise in a
document whose whole purpose is a bounded context budget.

### 2.6 Nothing checks the *content* of a validation record — INFERRED

`session validation record` takes `--status passed --exit-code 0` as arguments.
The runs in this repository passed the real exit code of the real command, but
the loop cannot tell that apart from an agent that types `--status passed` after
a red test-suite. The blind-spot check only looks for the string `phpunit`
somewhere in the session text (`VALIDATION_MARKERS`, same file as 2.1).

This is arguably by design - agent-loop states that it does not replace tests -
but it means "validation recorded" and "validation happened" are different
claims, and only the first one is enforced.

*Suggestion for agent-loop:* let `session validation record` optionally run the
command itself, or store a hash of captured output, so the record is evidence
rather than a statement.

### 2.7 A closed run leaves the card outside the board — VERIFIED, low severity

The lane set is `BACKLOG -> READY -> DOING -> VERIFY -> BLOCKED`. There is no
`DONE` lane, so after `workflow close` the only way to get a finished card out of
`VERIFY` is `board card archive`, which hides it. Combined with 2.2, a board that
has finished eight cards looks exactly like a board that has finished none.

---

## 3. Cost

Per governed task the loop costs roughly 12 commands beyond the actual work
(`card update`, `card move` ×2, `map refresh`, `search-index build`,
`workflow plan`, `workflow approve`, `session validation record`, 1-3
`session record`, `session checkpoint`, `review blindspots`, `workflow learn`,
`verify`, `workflow close`, `workflow status --expect complete`,
`card archive`). For PHON-8, a documentation-only task, that overhead was larger
than the change. For PHON-1 it paid for itself with the `C`-after-`R` bug alone.

**INFERRED:** the loop is worth its cost for a task that changes behaviour, and
is mostly ceremony for a task that changes only prose. A documented "light" path
for docs-only tasks would help.

---

## 4. Recorded findings

| Finding | Scope | Conclusion |
| --- | --- | --- |
| `phonetic.rule_table_consistency` | `src/voku/helper` | A rule table transcribed into a docblock must be cross-checked against its cited source and pinned per row; a self-contradicting table is a bug signal. |
| `phonetic.diacritic_fold_or_expand` | `src/voku/helper` | Whether a diacritic is folded or expanded depends on how users type the language, not on the sound. |
| `agent-loop.self_declared_gates` | `.agent-loop` | Two agent-loop gates are satisfied by a string the agent writes itself; treat them as prompts, not as proof. |

The first two are product findings and were consumed by later runs. The third is
the one to carry back into `voku/agent-loop`.

## 5. Next

Everything above is an observation about the tool, not a change to it. The
follow-up belongs in `voku/agent-loop` itself: 2.1, 2.2, 2.3 and 2.4 are the
four that are worth an issue there.
