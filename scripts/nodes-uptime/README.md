# Shido Validator Uptime Checker

This PHP script interacts with a Shido blockchain node API to gather information about validators and their uptime. It allows you to easily display the validators' uptime in a table format.

## Features

- Fetches the list of validators and their signing info (missed blocks data) from the API.
- Calculates the uptime based on missed blocks.
- Displays the validators' information in a table format.

## Usage

1. Clone or download the repository.
2. Make sure you have PHP installed on your machine.
3. Open the script in a text editor or IDE.
4. Customize the API URL and total reference block count variables if needed.
5. Run the script using the command `php script.php`.
6. The validators' uptime will be displayed in the console.

## Requirements

- PHP 7.0 or higher
- cURL extension enabled

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvement, please open an issue or submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).