<?php
// Define available API URLs for selection
$api_servers = [
    "https://swagger.shidoscan.com" => "Shidoscan Swagger",
    "https://shido-api.applejuice.256x25.tech" => "Applejuice API",
    "https://api-maverick.mavnode.io" => "Maverick API",
    "https://api.shido.indonode.net" => "Indonode API",
    "https://api.kenseishido.com" => "Kensei API"
];

// Default API URL (initial)
$api_url = "https://api-maverick.mavnode.io";

// Check if the API has been changed through the form submission
if (isset($_POST['api_server'])) {
    $api_url = $_POST['api_server'];
}

// Function to fetch the current block height using the correct structure
function get_current_block_height($api_url) {
    $endpoint = "/cosmos/base/tendermint/v1beta1/blocks/latest";
    $response = send_request($api_url . $endpoint);

    // Extract the block height from the response
    return isset($response['block']['header']['height']) ? (int) $response['block']['header']['height'] : null;
}

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

// Function to calculate uptime based on missed blocks in the last 100,000 blocks
function calculate_uptime($index_offset, $missed_blocks_counter, $reference_block_height, $current_block_height) {
    // Calculate total blocks in the last 100,000 blocks
    $total_blocks_in_window = $current_block_height - $reference_block_height;

    // Adjust missed blocks only within the last 100,000 blocks
    $missed_blocks_in_window = min($missed_blocks_counter, $total_blocks_in_window);

    // Calculate uptime
    $uptime = (($total_blocks_in_window - $missed_blocks_in_window) / $total_blocks_in_window) * 100;

    // Ensure uptime does not exceed 100%
    return min($uptime, 100);
}

// Frozen blocks data
$frozenBlocks = [
    ["validator" => "Shido Three", "missedBlocks" => 22090],
    ["validator" => "Shido Two", "missedBlocks" => 21663],
    ["validator" => "NeoNode", "missedBlocks" => 19860],
    ["validator" => "🔥DefiMike🔥Restake🔥", "missedBlocks" => 17711],
    ["validator" => "Shido Five", "missedBlocks" => 16879],
    ["validator" => "Shido Australia Co. | Restake", "missedBlocks" => 13162],
    ["validator" => "🔥 SHIDOFORGE ⚒ 🔥 REStake", "missedBlocks" => 13148],
    ["validator" => "🚀 WHEN MOON 🌕 WHEN LAMBO 🔥 RESTAKE ✅", "missedBlocks" => 12403],
    ["validator" => "🇫🇷 ShidoFrance One 🇫🇷", "missedBlocks" => 12257],
    ["validator" => "Scafire Node 5% Fee REStake Fast ❤️", "missedBlocks" => 12052],
    ["validator" => "SHIDO4LIFE 🥋🤺👊🏼 | ✅ REStake", "missedBlocks" => 11887],
    ["validator" => "Olim 🥷 VIP Services RESTAKE", "missedBlocks" => 11309],
    ["validator" => "🟢 Shidoverse  🔥  REStake  🚀", "missedBlocks" => 10851],
    ["validator" => "ShidoGuard", "missedBlocks" => 8593],
    ["validator" => "CryptoWav3z", "missedBlocks" => 8364],
    ["validator" => "BG-SHI-VAL01🦁 | REStake", "missedBlocks" => 7960],
    ["validator" => "Indonode | Restake", "missedBlocks" => 6563],
    ["validator" => "Shido Seven", "missedBlocks" => 5914],
    ["validator" => "ChainTools", "missedBlocks" => 4844],
    ["validator" => "Blue Trust 🤝", "missedBlocks" => 4280],
    ["validator" => "🇳🇱ShidoDutch🇳🇱 |REStake", "missedBlocks" => 3718],
    ["validator" => "ShidoObserver || REStake", "missedBlocks" => 3713],
    ["validator" => "🇸🇪 In Bjorn We Trust", "missedBlocks" => 3197],
    ["validator" => "Shido Six", "missedBlocks" => 3125],
    ["validator" => "FrankLinvesting📈 | RESTAKE☆", "missedBlocks" => 3112],
    ["validator" => "KENSEI ⚔️", "missedBlocks" => 3041],
    ["validator" => "Maverick | MavNode - REStake", "missedBlocks" => 1389],
    ["validator" => "Fox Node🦊 | RESTAKE", "missedBlocks" => 743],
    ["validator" => "Blockval | Restake", "missedBlocks" => 463],
    ["validator" => "Superbit123 & DAOverse | REStake~", "missedBlocks" => 276],
    ["validator" => "JquickDeFi", "missedBlocks" => 148],
    ["validator" => "256x25 /reStake", "missedBlocks" => 145],
    ["validator" => "MetaDefi || RESTAKE", "missedBlocks" => 86],
    ["validator" => "Nuke Node | RESTAKE", "missedBlocks" => 0],
    ["validator" => "xshrimp", "missedBlocks" => 0],
    ["validator" => "MagicShidoNode 🧙‍♂️🔮🌟🪄 | ✅ REStake", "missedBlocks" => 0],
    ["validator" => "Shido Four", "missedBlocks" => 0],
    ["validator" => "Neev Labs", "missedBlocks" => 0]
];

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

// Function to get frozen blocks for a given validator name
function get_frozen_blocks($validator_name, $frozenBlocks) {
    foreach ($frozenBlocks as $frozen) {
        if ($frozen['validator'] === $validator_name) {
            return $frozen['missedBlocks'];
        }
    }
    return 0; // Return 0 if no frozen blocks found
}

// Function to display validators' uptime, name, and missed blocks in an ordered list sorted by uptime
function display_uptime_sorted($api_url, $validators_map, $frozenBlocks) {
    // Fetch current block height
    $current_block_height = get_current_block_height($api_url);
    if ($current_block_height === null) {
        echo "Error: Unable to fetch the current block height.\n";
        return;
    }

    // Set reference block height to current block height - 100,000
    $reference_block_height = $current_block_height - 100000;

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
            $index_offset = $info['index_offset'];
            $missed_blocks_counter = $info['missed_blocks_counter'];

            // Get frozen blocks for this validator
            $frozen_blocks = get_frozen_blocks($validator_name, $frozenBlocks);

            // Subtract frozen blocks from the missed blocks counter
            $adjusted_missed_blocks = max(0, $missed_blocks_counter - $frozen_blocks);

            // Calculate uptime using the reference block height (current block - 100,000)
            $uptime_percentage = calculate_uptime($index_offset, $adjusted_missed_blocks, $reference_block_height, $current_block_height);
        } else {
            // If no match was found, set missed blocks and uptime to N/A
            $uptime_percentage = 0;
            $adjusted_missed_blocks = 'No signing info available';
        }

        // Add the data to the array
        $validator_data[] = [
            'name' => $validator_name,
            'consensus_address' => $consensus_address,
            'missed_blocks' => $adjusted_missed_blocks,
            'uptime' => $uptime_percentage
        ];
    }

    // Sort the array by uptime in descending order
    usort($validator_data, function($a, $b) {
        return $b['uptime'] <=> $a['uptime'];
    });

    // Display the sorted data in an ordered list (1, 2, 3...)
    echo "<table border='1' cellpadding='5' cellspacing='0' style='color: white; background-color: black; margin: 0 auto;'>";
    echo "<tr style='background-color: #333; color: white;'><th>#</th><th>Validator</th><th>Missed Blocks</th><th>Uptime (%)</th></tr>";

    // Iterate through the sorted data and display it
    foreach ($validator_data as $index => $data) {
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>";  // Display the rank (1, 2, 3...)
        echo "<td>" . $data['name'] . "</td>";
        
        echo "<td>" . $data['missed_blocks'] . "</td>";
        echo "<td>" . number_format($data['uptime'], 2) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

// Include the CSS styles for the entire page
echo '<style>';
echo 'body, html {';
echo '    margin: 0;';
echo '    padding: 0;';
echo '    height: 100%;';
echo '    background-color: black;'; // Ensure the background is black
echo '    color: white;'; // Ensure text color is white for visibility
echo '    font-family: "Open Sans", sans-serif;';
echo '}';
echo 'h1, p, a, label, span, table, tr, td {';
echo '    font-family: "Open Sans", sans-serif;';
echo '}';

echo 'table {';
echo '    width: 80%;'; // Control the table width
echo '    margin: 20px auto;'; // Center the table horizontally and give some spacing from the top
echo '    background-color: black;'; // Table background black
echo '    border-collapse: collapse;'; // Collapses the border
echo '}';
echo 'th, td {';
echo '    border: 1px solid #fff;'; // White borders for visibility
echo '    padding: 8px;'; // Padding for table cells
echo '    text-align: left;'; // Align text to the left
echo '}';
echo 'th {';
echo '    background-color: #333;'; // Darker background for headers for better visibility
echo '}';
echo 'select, input[type="button"], button {';
echo '    margin-top: 10px;'; // Space above the form elements
echo '    padding: 5px;'; // Padding inside form elements
echo '}';
echo 'label {';
echo '    margin-right: 5px;'; // Space between label and input
echo '}';
echo '</style>';

// Output the page content above the table
echo '<div style="text-align: center; padding-top: 20px;">';
echo '<h1>Shido Validator Uptime Monitor</h1>';
echo '<form method="POST">';
echo '<label for="api_server">Select API Server:</label>';
echo '<select name="api_server" id="api_server">';
foreach ($api_servers as $url => $name) {
    $selected = ($url === $api_url) ? 'selected' : '';
    echo "<option value='$url' $selected>$name</option>";
}
echo '</select>';
echo '<button type="submit">Change API Server</button>';
echo '<button onclick="location.reload();">Refresh Data</button>';
echo '</form>';



echo '</div>';



// Only call the function to display the table if a refresh is not requested
if (!isset($_POST['refresh'])) {
    display_uptime_sorted($api_url, $validators_map, $frozenBlocks);
}

// Handle the refresh button separately to update the uptime information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['refresh'])) {
    display_uptime_sorted($api_url, $validators_map, $frozenBlocks);
}

?>
