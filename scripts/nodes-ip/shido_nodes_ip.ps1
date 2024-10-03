# Define the list of URLs to retrieve data from
$urls = @(
    "http://18.192.75.125:26657/net_info?",
    "http://18.199.28.209:26657/net_info?",
    "http://3.79.211.195:26657/net_info?",
    "http://35.182.147.124:26657/net_info?",
    "http://15.156.158.51:26657/net_info?"
)

# Path to store/load the IPs from previous runs (same folder as the script)
$ipFilePath = Join-Path -Path $PSScriptRoot -ChildPath "previousIPs.json"

# Load the IPs from the last run, if the file exists in the same folder as the script
$previousIPs = @()
if (Test-Path $ipFilePath) {
    $previousIPs = Get-Content -Path $ipFilePath | ConvertFrom-Json
    Write-Host "`nLoaded previous IPs from $ipFilePath"
} else {
    Write-Host "`nNo previous IPs file found. This is the first run."
}

# Initialize an empty array to store all IP addresses and monikers gathered from URLs
$allNodesInfo = @()

# Function to validate IP addresses
function Is-ValidIPAddress {
    param (
        [string]$ip
    )
    # Exclude non-routable addresses
    return -not ($ip -eq "0.0.0.0" -or $ip -eq "127.0.0.1" -or
                 $ip.StartsWith("10.") -or $ip.StartsWith("172.16.") -or
                 $ip.StartsWith("172.17.") -or $ip.StartsWith("172.18.") -or
                 $ip.StartsWith("172.19.") -or $ip.StartsWith("172.20.") -or
                 $ip.StartsWith("172.21.") -or $ip.StartsWith("172.22.") -or
                 $ip.StartsWith("172.23.") -or $ip.StartsWith("172.24.") -or
                 $ip.StartsWith("172.25.") -or $ip.StartsWith("172.26.") -or
                 $ip.StartsWith("172.27.") -or $ip.StartsWith("172.28.") -or
                 $ip.StartsWith("172.29.") -or $ip.StartsWith("172.30.") -or
                 $ip.StartsWith("172.31.") -or
                 $ip.StartsWith("192.168."))
}

# Loop through each URL to gather IP addresses and monikers
foreach ($url in $urls) {
    try {
        # Fetch the webpage content
        $response = Invoke-WebRequest -Uri $url -ErrorAction Stop

        # Parse the JSON content
        $jsonData = $response.Content | ConvertFrom-Json

        # Loop through each peer node information
        foreach ($peer in $jsonData.result.peers) {
            $ip = $peer.remote_ip
            $moniker = $peer.node_info.moniker
            
            # Validate and add to the global array if it's a valid IP
            if (Is-ValidIPAddress -ip $ip) {
                $allNodesInfo += [PSCustomObject]@{
                    IP = $ip
                    Moniker = $moniker
                }
            }
        }
    } catch {
        Write-Warning "Failed to fetch data from $url"
    }
}

# Output the complete list of gathered IP addresses and monikers
Write-Host "All gathered IP addresses and monikers:" -ForegroundColor Yellow
$allNodesInfo

# Remove duplicates to get unique IP and moniker pairs
$uniqueNodesInfo = $allNodesInfo | Sort-Object -Property IP -Unique

# Output the unique list of IP addresses and monikers
Write-Host "`nUnique IP addresses and monikers:" -ForegroundColor Cyan
$uniqueNodesInfo

# Compare gathered IPs against the previously stored list (from last run)
$newIPs = @()
$existingIPs = @()

foreach ($node in $uniqueNodesInfo) {
    if ($previousIPs -contains $node.IP) {
        $existingIPs += $node
    } else {
        $newIPs += $node
    }
}

# Output the comparison results
Write-Host "`nNodes already in the previous list:" -ForegroundColor Magenta
$existingIPs

Write-Host "`nNew Nodes (not in the previous list):" -ForegroundColor Green
$newIPs

# Create output text for the report
$output = @"
All gathered IP addresses and monikers:
$($allNodesInfo | Out-String)

`nUnique IP addresses and monikers (after removing duplicates):
$($uniqueNodesInfo | Out-String)

`nNodes already in the previous list:
$($existingIPs | Out-String)

`nNew Nodes (not in the previous list):
$($newIPs | Out-String)
"@

# Export results to the script's directory
$outputFilePath = Join-Path -Path $PSScriptRoot -ChildPath "IPGatheringResults.txt"
if ($uniqueNodesInfo.Count -gt 0) {
    # Export results to the script's directory
    $output | Out-File -FilePath $outputFilePath -Encoding UTF8

    # Confirm that the file has been saved
    Write-Host "`nResults exported to $outputFilePath"
} else {
    Write-Warning "No IP gathering results obtained."
}

# Save the current unique IP addresses to the script's directory for future runs
$uniqueNodesInfo | ConvertTo-Json | Out-File -FilePath $ipFilePath -Encoding UTF8
Write-Host "`nUnique IP addresses and monikers saved to $ipFilePath"
