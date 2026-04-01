#!/usr/bin/env bash
set -euo pipefail

raw_input="$(cat)"

if [[ -z "${raw_input// }" ]]; then
  exit 0
fi

if echo "$raw_input" | grep -Eiq 'PostToolUse' && echo "$raw_input" | grep -Eiq 'apply_patch|create_file|edit_notebook_file'; then
  cat <<'JSON'
{"continue":true,"systemMessage":"Auto-review: Code was just saved/edited. Run a quick self-review for regressions, permission checks, and missing tests."}
JSON
fi

exit 0
