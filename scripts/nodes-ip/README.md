# PowerShell Script: Gathering Network Information for Shido Blockchain

## Overview

This PowerShell script is designed to retrieve and analyze network information from multiple nodes of the **Shido** blockchain, a Layer 1 blockchain built on the **Cosmos** architecture. The script aims to gather IP addresses from specified URLs, validate them against a reference list, identify duplicates, and check the availability of a specific port. The results are then exported to a text file for easy access and reporting.

## Features

- **Data Retrieval**: Fetches network information from a predefined list of Shido blockchain nodes.
- **IP Validation**: Filters out non-routable and invalid IP addresses to ensure the accuracy of gathered data.
- **Duplicate Identification**: Detects and lists duplicate IP addresses from the gathered information.
- **Comparison with Existing IPs**: Checks newly gathered IPs against a reference list to identify new nodes in the network.
- **Port Availability Check**: Tests the availability of port 26656 on newly identified IP addresses to ensure they are reachable.
- **Export Report**: Compiles results and saves them to a text file on the user's desktop for review.

## Components

1. **Node URL List**  
The script includes a list of URLs from which to fetch network information about Shido blockchain nodes:
    ```powershell
    $urls = @(
        "http://18.192.75.125:26657/net_info?",
        "http://18.199.28.209:26657/net_info?",
        "http://3.79.211.195:26657/net_info?",
        "http://35.182.147.124:26657/net_info?",
        "http://15.156.158.51:26657/net_info?"
    )
    ```

2. **Reference IP List**  
A predefined list of existing IP addresses relevant to the Shido network for comparison kept into ``previousIPs.json``:
    ```powershell
    "109.199.104.27",
    "109.199.127.133",
    "116.202.218.189",
    "135.181.225.155",
    "142.160.67.89",
    "149.102.132.87",
    "149.202.108.206",
    "15.156.158.51",
    "15.157.50.94",
    "154.12.230.61",
    ```

3. **Functions**  
   - **Test-PortLocally**: Checks if a specified port (default is 26656) is open on a given IP address, ensuring the nodes are reachable.
   - **Is-ValidIPAddress**: Validates IP addresses to exclude non-routable addresses.

4. **Main Execution**  
   - The script loops through the URLs, retrieves IP addresses, and filters them for validity.
   - Identifies duplicates and distinguishes between existing and new IPs within the Shido network.
   - Conducts a port availability check on the new IP addresses.

5. **Output**  
The results, including gathered IP addresses, duplicates, unique entries, and port check results, are compiled and saved to a text file:
    ```plaintext
    IPGatheringResults.txt
    ```

## Usage

To execute the script, save it as a `.ps1` file and run it in a PowerShell environment. Ensure that you have the necessary permissions to access the URLs and that your network allows outbound connections.

## Notes

- Make sure PowerShell is set to allow script execution. You may need to change the execution policy using the command:
    ```powershell
    Set-ExecutionPolicy RemoteSigned
    ```
- Customize the `$urls` and `$existingIPList` variables as needed for your specific Shido network requirements.
