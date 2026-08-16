<!-- agent-loop:project-instructions:begin -->
## agent-loop workflow router

This repository uses `voku/agent-loop` for governed coding work. Keep this file as a small router; detailed procedures live in the installed skills and CLI help.

When this router is projected into a host instruction file, the content between the `agent-loop:project-instructions` markers is package-managed. Put project-specific rules outside those markers and refresh the managed block with `init install-assets` or `init sync-instructions` instead of editing it by hand.

For non-trivial coding, review, debugging, or repository-maintenance tasks:

- At the start of a fresh agent session, or when the agent-loop setup is unknown, run `tools/agent-loop/vendor/bin/agent-loop init status` once. Its `Activation:` section reports the resolved CLI path, whether skills are projected into a host at all, and whether local Git hooks and the commit template are active; its `Next:` lines are the exact activation commands for this repository. Run them instead of silently bypassing the setup, and use `tools/agent-loop/vendor/bin/agent-loop init doctor` for the wider environment. If the CLI itself is missing, install the Composer dependencies first. Do not repeat bootstrap diagnostics once the current session has established the setup.
- Use the installed `agent-loop-*` skills and `agent-recall-consumer` when their descriptions match the task. Do not recreate their procedures as ad-hoc prompt text.
- Prefer the owning package's typed PHP API inside workflow orchestration; do not serialize a dependency result merely to parse it again in the same PHP process. At an agent-facing CLI boundary, prefer the smallest lossless projection the owner provides and use `--format=toon` for structured read-only output when available. Keep canonical JSON for durable, hashed, replayable, or explicitly interoperable artifacts.
- Use `agent-map` for bounded source discovery before broad reads. When map-backed Recall matters, build or refresh the map and search index before `workflow approve`, because approval compiles Recall from evidence that already exists.
- When a task has a durable Contract or task id, inspect `tools/agent-loop/vendor/bin/agent-loop workflow status <task-id> --format=toon` before mutation and continue from persisted state rather than conversational memory.
- Select applicable L2 operating prompts through the Contract / `agent-recall-compiler` before approval, then read the compiled `system.md` in the Recall output directory that `workflow approve` names. A compiled prompt is not evidence of use unless the acting agent actually received the generated Recall briefing.
- For local commits, activate the package Git hooks with the `init status` command shown above when the repository expects them and let them execute. GitHub/API connector writes cannot run local `pre-commit` or `commit-msg` hooks; report those hooks as not exercised rather than claiming they passed.
- Run the validation declared by the governed task and preserve exact command/output evidence. Do not turn workflow completion into a merged, shipped, or released claim without exact Git candidate evidence.
<!-- agent-loop:project-instructions:end -->
