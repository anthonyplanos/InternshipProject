$rawInput = [Console]::In.ReadToEnd()

if ([string]::IsNullOrWhiteSpace($rawInput)) {
    exit 0
}

$toolUsePattern = 'apply_patch|create_file|edit_notebook_file'

if (($rawInput -match 'PreToolUse') -and ($rawInput -match $toolUsePattern)) {
    $output = @{
        hookSpecificOutput = @{
            hookEventName = 'PreToolUse'
            permissionDecision = 'ask'
            permissionDecisionReason = 'Confirm before applying code edits.'
        }
    }

    $output | ConvertTo-Json -Compress
    exit 0
}

if (($rawInput -match 'PostToolUse') -and ($rawInput -match $toolUsePattern)) {
    $output = @{
        continue = $true
        systemMessage = 'Auto-review gate: Code was just saved/edited. Ask the user for confirmation before running auto-review. Only proceed if the user explicitly says yes.'
    }

    $output | ConvertTo-Json -Compress
}

exit 0
