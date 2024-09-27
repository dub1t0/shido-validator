# Shido Validator Uptime Monitor

The Shido Validator Uptime Monitor is a PHP-based tool designed to monitor and display the uptime statistics of various blockchain validators connected through different API endpoints. The tool fetches data like current block height and signing information to calculate and display uptime percentages for each validator.

## Features

- **Dynamic API Selection**: Users can switch between multiple pre-defined API servers to check validator statuses across different blockchain nodes.
- **Real-Time Data Refresh**: A refresh button allows users to update the displayed data without refreshing the entire page.
- **Uptime Calculation**: Calculates the uptime percentage for each validator based on missed blocks over the last 100,000 blocks.
- **Responsive Design**: Utilizes Bootstrap 5 for a responsive layout, making it accessible on various devices.

## How It Works

1. **API Server Selection**: The application allows users to select from a list of predefined API servers. This selection determines from which server the validator data is fetched.
2. **Fetching Data**: 
   - The current block height and signing info (missed blocks data) for all validators are fetched using the selected API server.
   - Functions are in place to handle HTTP GET requests and to process JSON responses appropriately.
3. **Calculating Uptime**:
   - Uptime is calculated by comparing the number of blocks a validator was supposed to sign versus the ones they actually signed, adjusted for any 'frozen blocks' where data may not be updated.
   - Results are displayed in a sorted table, showing validators with the best uptime first.

## Setup

To set up the Shido Validator Uptime Monitor on your server, follow these steps:

- Clone the repository to your PHP-enabled server.
- Ensure you have cURL enabled in your PHP environment as it's used for API requests.
- Access the project via a web browser to view the uptime monitor interface.

## Usage

- **Change API Server**: Use the dropdown to select the API server you wish to query.
- **Refresh Data**: Click this button to reload data from the currently selected API server without reloading the entire webpage.

## Technologies Used

- **PHP**: Server-side scripting.
- **Bootstrap 5**: For responsive frontend components.
- **cURL**: For making API requests.
- **HTML/CSS**: Markup and styling.

## Source Code Structure

Here's a brief overview of the main functional blocks in the source code:

- **API Servers Configuration**: A PHP associative array defines the available API endpoints.
- **API Request Handling**: Functions to send HTTP GET requests and process JSON responses.
- **Data Display**: Functions to calculate and display data in a user-friendly table format.
- **Styling**: Inline CSS for custom styling and responsiveness.

## Contributors

- This project is currently maintained by ShidoFrance.
- Contributions, bug reports, and enhancements are welcome.
