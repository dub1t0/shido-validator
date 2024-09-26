<?php
// API URL of the Shido blockchain node
$api_url = "https://api-maverick.mavnode.io";

// Function to fetch the signing info (missed blocks data) for all validators
function get_all_signing_info($api_url) {
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

// Function to calculate uptime using start_height, index_offset, and missed_blocks_counter
function calculate_uptime($start_height, $index_offset, $missed_blocks_counter) {
    // Calculate the total blocks
    $total_blocks = $index_offset - $start_height;

    // Avoid division by zero
    if ($total_blocks <= 0) {
        return 0;
    }

    // Calculate the uptime
    $uptime = (($total_blocks - $missed_blocks_counter) / $total_blocks) * 100;

    // Ensure uptime does not exceed 100%
    return min($uptime, 100);
}

// Define a map of validator names to their known consensus (signer) addresses
$validators_map = [
    "💩💩💩💩" => "shidovalcons1qxjmnfjfq3q3yyxn6tdz7vkw7xj0k2pjmpte72",
    "ChainTools" => "shidovalcons1qsy4hmxqs38eapk00styvdt5qtz3xv3j3d949q",
    "Olim 🥷 VIP Services RESTAKE" => "shidovalcons1prcrqn2kwlh77s0ktunua82qe77dv82slc74he",
    "Carnival Consensus 🎪🎪🎪" => "shidovalcons1perda8fa3ce3xwg2p6zgl7a0akfy2ln56xn648",
    "🚀 WHEN MOON 🌕 WHEN LAMBO 🔥 RESTAKE ✅" => "shidovalcons1pl5ax5thhns3ktegyc4jgsjyrgjxwrmmcp882v",
    "SHIDO4LIFE 🥋🤺👊🏼 | ✅ REStake" => "shidovalcons1zxk4rk7a05ryukmrn6y6l0qgzuw40kv58c2wtx",
    "Sherlock Holmes" => "shidovalcons1zwg0zjaqch52aujnh5cw4pk74y3f8qm7dc655a"
];

// Function to display validators' uptime, name, and missed blocks in an ordered list sorted by uptime
function display_uptime_sorted($api_url, $validators_map) {
    // Fetch signing info for all validators (for missed blocks)
    $signing_info = get_all_signing_info($api_url);

    if (empty($signing_info)) {
        echo "Error: No data found.\n";
        return;
    }

    // Create a mapping of signing info by consensus address
    $signing_info_map = [];
    foreach ($signing_info as $info) {
        $signing_info_map[$info['address']] = $info;
    }

    // Create an array to hold validator data
    $validator_data = [];

    // Iterate through the provided validators map (name => address)
    foreach ($validators_map as $validator_name => $consensus_address) {
        // Check if we have signing info for this validator's consensus address
        if (isset($signing_info_map[$consensus_address])) {
            $info = $signing_info_map[$consensus_address];

            // Extract the necessary data from signing info
            $start_height = $info['start_height'];
            $index_offset = $info['index_offset'];
            $missed_blocks_counter = $info['missed_blocks_counter'];

            // Calculate uptime using the new method
            $uptime_percentage = calculate_uptime($start_height, $index_offset, $missed_blocks_counter);
        } else {
            // If no match was found, set missed blocks and uptime to N/A
            $uptime_percentage = 0;
            $missed_blocks_counter = 'No signing info available';
        }

        // Add the data to the array
        $validator_data[] = [
            'name' => $validator_name,
            'consensus_address' => $consensus_address,
            'missed_blocks' => $missed_blocks_counter,
            'uptime' => $uptime_percentage
        ];
    }

    // Sort the array by uptime in descending order
    usort($validator_data, function($a, $b) {
        return $b['uptime'] <=> $a['uptime'];
    });

    // Display the sorted data in an ordered list (1, 2, 3...)
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>#</th><th>Validator</th><th>Consensus Address</th><th>Missed Blocks</th><th>Uptime (%)</th></tr>";

    // Iterate through the sorted data and display it
    foreach ($validator_data as $index => $data) {
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>";  // Display the rank (1, 2, 3...)
        echo "<td>" . $data['name'] . "</td>";
        echo "<td>" . $data['consensus_address'] . "</td>";
        echo "<td>" . $data['missed_blocks'] . "</td>";
        echo "<td>" . number_format($data['uptime'], 2) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

// Run the script with the predefined validator map
display_uptime_sorted($api_url, $validators_map);
?>
