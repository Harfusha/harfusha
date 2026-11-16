$ErrorActionPreference = 'Stop'
$stateDir = Join-Path $env:LOCALAPPDATA 'HarfushaGithubSync'
$tokenPath = Join-Path $stateDir 'token.dpapi'
$statusPath = Join-Path $stateDir 'last-run.json'
try {
    Add-Type -AssemblyName System.Security
    $protected = [IO.File]::ReadAllBytes($tokenPath)
    $tokenBytes = [Security.Cryptography.ProtectedData]::Unprotect(
        $protected, $null, [Security.Cryptography.DataProtectionScope]::CurrentUser)
    $token = [Text.Encoding]::UTF8.GetString($tokenBytes)
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    $response = Invoke-RestMethod -Uri 'https://adam-halfar.cz/cron/github' -Method Post `
        -Headers @{ 'X-Cron-Token' = $token } -TimeoutSec 120
    if ($null -eq $response.imported -or $null -eq $response.repositories) {
        throw 'Invalid synchronization response'
    }
    @{ status = 'success'; completed_utc = [DateTime]::UtcNow.ToString('o');
       imported = $response.imported; repositories = $response.repositories } |
        ConvertTo-Json -Compress | Set-Content -LiteralPath $statusPath -Encoding UTF8
} catch {
    @{ status = 'failed'; completed_utc = [DateTime]::UtcNow.ToString('o');
       error_type = $_.Exception.GetType().Name } |
        ConvertTo-Json -Compress | Set-Content -LiteralPath $statusPath -Encoding UTF8
    exit 1
} finally {
    if ($tokenBytes) { [Array]::Clear($tokenBytes, 0, $tokenBytes.Length) }
}
