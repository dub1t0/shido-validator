<?php
// API URL of the Shido blockchain node
$api_url = "https://api-maverick.mavnode.io";
$reference_blocks = 100000;  // Total reference block count for uptime calculation

// Function to fetch the list of validators
function get_validators($api_url) {
    $endpoint = "/cosmos/staking/v1beta1/validators";
    $response = send_request($api_url . $endpoint);
    return isset($response['validators']) ? $response['validators'] : [];
}

// Function to fetch the signing info (missed blocks data)
function get_signing_info($api_url) {
    $endpoint = "/cosmos/slashing/v1beta1/signing_infos";
    $response = send_request($api_url . $endpoint);
    return isset($response['info']) ? $response['info'] : [];
}

// Function to send HTTP GET request using cURL
function send_request($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Function to calculate uptime based on missed blocks
function calculate_uptime($missed_blocks, $reference_blocks) {
    return max(0, 100 * (1 - ($missed_blocks / $reference_blocks)));
}

// Function to display validators' uptime
function display_uptime($api_url, $reference_blocks) {
    // Fetch validators and signing info
    $validators = get_validators($api_url);
    $signing_info = get_signing_info($api_url);

    if (empty($validators)) {
        echo "Error: No validators found.\n";
        return;
    }

    // Create a mapping of validator consensus addresses to missed blocks
    $missed_blocks_map = [];
    foreach ($signing_info as $info) {
        $address = $info['address'];
        $missed_blocks = $info['missed_blocks_counter'];
        $missed_blocks_map[$address] = $missed_blocks;
    }

    // Display table header
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Validator</th><th>Status</th><th>Jailed</th><th>Missed Blocks</th><th>Uptime (%)</th></tr>";

    // Iterate through validators and display data
    foreach ($validators as $validator) {
        $moniker = htmlspecialchars($validator['description']['moniker']);  // Prevent HTML injection
        $status = ($validator['status'] === 'BOND_STATUS_BONDED') ? 'Bonded' : 'Unbonding';
        $jailed = $validator['jailed'] ? 'Yes' : 'No';

        // Get consensus address and missed blocks
        $consensus_pubkey = $validator['consensus_pubkey']['key'];
        $missed_blocks = isset($missed_blocks_map[$consensus_pubkey]) ? $missed_blocks_map[$consensus_pubkey] : 0;

        // Calculate uptime
        $uptime_percentage = calculate_uptime($missed_blocks, $reference_blocks);

        // Display each row
        echo "<tr>";
        echo "<td>$moniker</td>";
        echo "<td>$status</td>";
        echo "<td>$jailed</td>";
        echo "<td>$missed_blocks</td>";
        echo "<td>" . number_format($uptime_percentage, 2) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

// Run the script
display_uptime($api_url, $reference_blocks);
?>
