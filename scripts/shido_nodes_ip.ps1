# Define the list of URLs to retrieve data from
$urls = @(
    "http://18.192.75.125:26657/net_info?",
    "http://18.199.28.209:26657/net_info?",
    "http://3.79.211.195:26657/net_info?",
    "http://35.182.147.124:26657/net_info?",
    "http://15.156.158.51:26657/net_info?"
)

# Define the reference list of existing IPs
$existingIPList = @(
    "3.98.239.17",
    "3.110.11.92",
    "38.242.137.118",
    "82.9.116.86",
    "37.27.129.189",
    "45.76.119.193",
    "35.182.147.124",
    "85.190.246.81",
    "86.38.205.246",
    "167.235.2.101",
    "116.202.218.189",
    "18.193.227.128",
    "81.17.103.4",
    "38.242.228.143",
    "37.27.117.86",
    "3.76.57.158",
    "18.192.75.125",
    "45.10.163.234",
    "158.220.94.56",
    "185.230.138.22",
    "15.157.235.96",
    "38.242.144.176",
    "78.46.174.72",
    "97.91.90.171",
    "62.84.180.194",
    "135.181.225.155",
    "167.235.12.38",
    "18.182.78.42",
    "37.187.93.177",
    "207.244.253.62",
    "3.108.81.233",
    "3.7.240.62",
    "167.235.102.45",
    "18.178.17.86",
    "52.194.8.37",
    "15.157.50.94",
    "188.34.136.203",
    "142.160.67.89",
    "194.163.160.123",
    "52.193.158.166",
    "157.173.124.127",
    "195.179.229.249",
    "149.102.132.87",
    "195.26.242.233",
    "154.12.230.61",
    "18.199.28.209",
    "85.25.46.218",
    "169.1.35.42",
    "3.79.211.195",
    "194.147.58.103",
    "38.242.245.32",
    "18.184.249.140",
    "78.46.88.16",
    "86.10.87.228",
    "65.109.115.195",
    "149.102.132.87",
    "3.98.102.80"
)

# Initialize an empty array to store all IP addresses gathered from URLs
$allIPAddresses = @()

# Get the current user's desktop path
$desktopPath = [System.IO.Path]::Combine([System.Environment]::GetFolderPath("Desktop"), "IPGatheringResults.txt")

# Function to check if a port is open using local TCP connection
function Test-PortLocally {
    param (
        [string]$ip,
        [int]$port
    )

    try {
        # Attempt to create a TCP client connection
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $tcpClient.Connect($ip, $port)
        $tcpClient.Close()
        return "${ip}: Port ${port} is OPEN"
    } catch {
        return "${ip}: Port ${port} is CLOSED or UNREACHABLE"
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

# Compare gathered IPs against the existing reference list
$newIPs = @()
$existingIPs = @()

foreach ($ip in $uniqueIPAddresses) {
    if ($existingIPList -contains $ip) {
        $existingIPs += $ip
    } else {
        $newIPs += $ip
    }
}

# Output the comparison results
Write-Host "`nIPs already in the reference list:" -ForegroundColor Magenta
$existingIPs

Write-Host "`nNew IPs (not in the reference list):" -ForegroundColor Green
$newIPs

# Initialize an array to store the port check results
$portCheckResults = @()

# Check if port 26656 is open for each unique IP address
foreach ($ip in $uniqueIPAddresses) {
    if ($newIPs -contains $ip) {
        # Check port only for new IPs
        $result = Test-PortLocally -ip $ip -port 26656
        $portCheckResults += $result
    }
}

# Output the port check results
Write-Host "`nPort 26656 check results for each new IP address:" -ForegroundColor Blue
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

`nIPs already in the reference list:
$($existingIPs -join "`n")

`nNew IPs (not in the reference list):
$($newIPs -join "`n")

`nPort 26656 check results for each new IP address:
$($portCheckResults -join "`n")
"@

# Export results to the desktop
if ($portCheckResults.Count -gt 0) {
    # Export results to the desktop
    $output | Out-File -FilePath $desktopPath -Encoding UTF8

    # Confirm that the file has been saved
    Write-Host "`nResults exported to $desktopPath"
} else {
    Write-Warning "No port check results obtained."
}
