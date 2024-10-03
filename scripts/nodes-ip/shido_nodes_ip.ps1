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

# Initialize an empty array to store all IP addresses gathered from URLs
$allIPAddresses = @()

# Function to check if a port is open using local TCP connection and measure time
function Test-PortLocally {
    param (
        [string]$ip,
        [int]$port
    )

    try {
        # Measure the time it takes to check the port
        $executionTime = Measure-Command {
            # Attempt to create a TCP client connection
            $tcpClient = New-Object System.Net.Sockets.TcpClient
            $tcpClient.Connect($ip, $port)
            $tcpClient.Close()
        }
        
        # Return the result along with the time taken
        return "${ip}: Port ${port} is OPEN (Checked in $([math]::Round($executionTime.TotalSeconds, 2)) seconds)"
    } catch {
        return "${ip}: Port ${port} is CLOSED or UNREACHABLE (Checked in $([math]::Round($executionTime.TotalSeconds, 2)) seconds)"
    }
}

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

# Loop through each URL to gather IP addresses
foreach ($url in $urls) {
    try {
        # Fetch the webpage content
        $response = Invoke-WebRequest -Uri $url -ErrorAction Stop

        # Extract all IP addresses using regex pattern
        $ipPattern = "\b(?:\d{1,3}\.){3}\d{1,3}\b"
        $ipAddresses = Select-String -InputObject $response.Content -Pattern $ipPattern -AllMatches | ForEach-Object { $_.Matches } | ForEach-Object { $_.Value }

        # Filter out invalid IP addresses
        $validIPs = $ipAddresses | Where-Object { Is-ValidIPAddress -ip $_ }
        # Add the found valid IPs to the global array
        $allIPAddresses += $validIPs
    } catch {
        Write-Warning "Failed to fetch data from $url"
    }
}

# Output the complete list of gathered IP addresses (with possible duplicates)
Write-Host "All gathered IP addresses (including duplicates):" -ForegroundColor Yellow
$allIPAddresses

# Find duplicate IP addresses
$duplicateIPs = $allIPAddresses | Group-Object | Where-Object { $_.Count -gt 1 } | ForEach-Object { $_.Name }

# Output the list of duplicate IP addresses, if any
if ($duplicateIPs.Count -gt 0) {
    Write-Host "`nDuplicate IP addresses found:" -ForegroundColor Red
    $duplicateIPs
} else {
    Write-Host "`nNo duplicate IP addresses found." -ForegroundColor Green
}

# Remove duplicates to get the unique IP addresses
$uniqueIPAddresses = $allIPAddresses | Sort-Object -Unique

# Output the unique list of IP addresses
Write-Host "`nUnique IP addresses:" -ForegroundColor Cyan
$uniqueIPAddresses

# Compare gathered IPs against the previously stored list (from last run)
$newIPs = @()
$existingIPs = @()

foreach ($ip in $uniqueIPAddresses) {
    if ($previousIPs -contains $ip) {
        $existingIPs += $ip
    } else {
        $newIPs += $ip
    }
}

# Output the comparison results
Write-Host "`nIPs already in the previous list:" -ForegroundColor Magenta
$existingIPs

Write-Host "`nNew IPs (not in the previous list):" -ForegroundColor Green
$newIPs

# Initialize an array to store the port check results
$portCheckResults = @()

# Total number of IPs to check
$totalIPs = $uniqueIPAddresses.Count
$ipIndex = 0

# Check if port 26657 is open for each unique IP address
foreach ($ip in $uniqueIPAddresses) {
    # Update the progress bar
    $ipIndex++
    $percentComplete = [math]::Round(($ipIndex / $totalIPs) * 100)
    Write-Progress -Activity "Checking Port 26657" -Status "Processing IP $ipIndex of $totalIPs" -PercentComplete $percentComplete

    # Check port for all IPs
    $result = Test-PortLocally -ip $ip -port 26657
    $portCheckResults += $result
}

# Output the port check results
Write-Host "`nPort 26657 check results for each unique IP address:" -ForegroundColor Blue
foreach ($result in $portCheckResults) {
    Write-Host $result
}

# Create output text for the report
$output = @"
All gathered IP addresses (including duplicates):
$($allIPAddresses -join "`n")

`nDuplicate IP addresses found:
$($duplicateIPs -join "`n")

`nUnique IP addresses (after removing duplicates):
$($uniqueIPAddresses -join "`n")

`nIPs already in the previous list:
$($existingIPs -join "`n")

`nNew IPs (not in the previous list):
$($newIPs -join "`n")

`nPort 26657 check results for each unique IP address:
$($portCheckResults -join "`n")
"@

# Export results to the script's directory
$outputFilePath = Join-Path -Path $PSScriptRoot -ChildPath "IPGatheringResults.txt"
if ($portCheckResults.Count -gt 0) {
    # Export results to the script's directory
    $output | Out-File -FilePath $outputFilePath -Encoding UTF8

    # Confirm that the file has been saved
    Write-Host "`nResults exported to $outputFilePath"
} else {
    Write-Warning "No port check results obtained."
}

# Save the current unique IP addresses to the script's directory for future runs
$uniqueIPAddresses | ConvertTo-Json | Out-File -FilePath $ipFilePath -Encoding UTF8
Write-Host "`nUnique IP addresses saved to $ipFilePath"
