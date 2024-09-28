# Nodes Locations

## Overview
This web application enables the fetching and display of Nodes locations from various APIs. Users can select different API sources, view Nodes details, and see geolocated results sorted by location.

## Features
- **API Selection**: Users can choose from multiple predefined API endpoints to fetch Nodes data.
- **Dynamic Data Fetching**: The application dynamically retrieves IP data based on the selected API.
- **Geolocation Information**: It uses the `ipinfo.io` API to fetch detailed location information for each Nodes.
- **Error Handling**: Robust error handling to manage and report issues during data fetching and processing.
- **Sortable Results**: Displays Nodeses and their locations, with sorting capabilities based on geographical location.

## Technologies Used
- **HTML/CSS**: For structuring and styling the web interface.
- **JavaScript**: Used for frontend scripting, API requests, and DOM manipulation.
- **PHP**: Server-side scripting to handle requests securely, acting as a proxy for API calls.

## Setup and Installation
1. **Clone the Repository**:
   ```
   git clone https://github.com/yourusername/ip-address-locations.git
   ```
2. **Navigate to the Project Directory**:
   ```
   cd ip-address-locations
   ```
3. **Run a Local Server**:
   - You can use any server of your choice. For PHP, you can use:
     ```
     php -S localhost:8000
     ```
   - Open `http://localhost:8000` in your web browser to view the application.

## How to Use
- Select an API from the dropdown menu.
- Click **Refresh Data** to fetch Nodeses from the selected API.
- View the Nodes details and their fetched locations in the tables displayed.

## Contributing
Contributions are welcome, and any contributions you make are **greatly appreciated**.
- **Fork the Project**: Fork and clone the repo as mentioned above.
- **Create a Pull Request**: Make your changes in a new branch and submit a PR.

## License
Distributed under the MIT License. See `LICENSE` file for more information.

## Contact
Your Name – [@shidofrance](https://twitter.com/shidofrance) – your.email@example.com

## Acknowledgments
- Use this section to list resources you find helpful and would like to give credit to.
