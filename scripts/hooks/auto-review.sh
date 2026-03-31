#!/usr/bin/env bash
set -euo pipefail

raw_input="$(cat)"

if [[ -z "${raw_input// }" ]]; then
  exit 0
fi

if echo "$raw_input" | grep -Eiq 'PreToolUse' && echo "$raw_input" | grep -Eiq 'apply_patch|create_file|edit_notebook_file'; then
  cat <<'JSON'
{"hookSpecificOutput":{"hookEventName":"PreToolUse","permissionDecision":"ask","permissionDecisionReason":"Confirm before applying code edits."}}
JSON
  exit 0
fi

if echo "$raw_input" | grep -Eiq 'PostToolUse' && echo "$raw_input" | grep -Eiq 'apply_patch|create_file|edit_notebook_file'; then
  cat <<'JSON'
{"continue":true,"systemMessage":"Auto-review gate: Code was just saved/edited. Ask the user for confirmation before running auto-review. Only proceed if the user explicitly says yes."}
JSON
fi

exit 0
