$rawInput = [Console]::In.ReadToEnd()

if ([string]::IsNullOrWhiteSpace($rawInput)) {
    exit 0
}

$toolUsePattern = 'apply_patch|create_file|edit_notebook_file'

if (($rawInput -match 'PostToolUse') -and ($rawInput -match $toolUsePattern)) {
    $output = @{
        continue = $true
        systemMessage = 'Auto-review: Code was just saved/edited. Run a quick self-review for regressions, permission checks, and missing tests.'
    }

    $output | ConvertTo-Json -Compress
}

exit 0
