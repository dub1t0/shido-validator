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

// Updated map of validator names to their known consensus (signer) addresses
$validators_map = [
    "Superbit123 & DAOverse | REStake~" => "shidovalcons194tpuw3ncmtag4y0k2548hn3at37u3nkq8qljj",
    "Shido Six" => "shidovalcons1ug9ju8045plxlxt6c2jx5jwjk9myyl0dplv3zx",
    "Maverick | MavNode - REStake" => "shidovalcons1uhdt2plkwn09x6n6zjgsr84agmkgxmvq2jdkdn",
    "256x25 /reStake" => "shidovalcons1z6qd7g3u4capzj8qal8d0pf4d0p0kar52cs9t0",
    "ShidoObserver || REStake" => "shidovalcons1v7kk5mz095474l3m9vcnjvfcq2sn6h5qdhpc3g",
    "MetaDefi || RESTAKE" => "shidovalcons1khdy3qgj5puh8tns8txf7rpxf0lyxghegxk2fc",
    "KENSEI ⚔️" => "shidovalcons14zx0cxlmw9jkc36frv2aq4ch6knt0uneavqx58",
    "NeoNode" => "shidovalcons1ntn0gmetdzpny2eycqx556xhz2aht29pdx4f6l",
    "Nuke Node | RESTAKE" => "shidovalcons1prcrqn2kwlh77s0ktunua82qe77dv82slc74he",
    "Fox Node🦊 | RESTAKE" => "shidovalcons1khzdyvqdg6c6e66g7upkr3f7jk6fvy75qdeajw",
    "FrankLinvesting📈 | RESTAKE☆" => "shidovalcons1xdjmmzc6krsdpzqu4k77vx87tff0qf2htn4lte",
    "Shido Seven" => "shidovalcons1mmv883tkjew7ygffgv6jf00x28mzpxp6csydx5",
    "🚀 WHEN MOON 🌕 WHEN LAMBO 🔥 RESTAKE ✅" => "shidovalcons13gup7dwmm62tc57v6qyr70k8pykly2p9r723ek",
    "Olim 🥷 VIP Services RESTAKE" => "shidovalcons1czjpws0ec2ehvzwg3g52ss4d6s07p65vzn27n0",
    "🔥DefiMike🔥Restake🔥" => "shidovalcons1e5z8sxyx0nvuprl5d63cayun7z05frwqxfua0k",
    "Neev Labs" => "shidovalcons1qxjmnfjfq3q3yyxn6tdz7vkw7xj0k2pjmpte72",
    "🇳🇱ShidoDutch🇳🇱 |REStake" => "shidovalcons1jl70udxd5872m8ux0xvv7uaf7zzypf63f42rvf",
    "ShidoGuard" => "shidovalcons1zxk4rk7a05ryukmrn6y6l0qgzuw40kv58c2wtx",
    "🇫🇷 ShidoFrance One 🇫🇷" => "shidovalcons1xfv2anmpj7dlh06cz0rg9jx2nswn0v5a24rprm"
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

// Handle page refresh with a button
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // When the page is refreshed via the button click
    display_uptime_sorted($api_url, $validators_map);
}

// Add a refresh button to reload the page and gather updated uptime
echo '<form method="POST" action="">';
echo '<button type="submit">Refresh Uptime</button>';
echo '</form>';
?>
