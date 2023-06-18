# Effectra\Http\Extensions

Effectra\Http\Extensions is a PHP package that provides extensions and utilities for working with HTTP requests and responses.

## Installation

You can install this package via Composer. Run the following command in your project directory:

```bash
composer require effectra/http-extensions
```

## Usage

### RequestExtension

The `RequestExtension` class extends the `Effectra\Http\Message\ServerRequest` class and provides additional methods for working with HTTP requests.

#### Creating a Request

You can create a new request using the `RequestExtension::fromGlobal()` method, which creates a request object based on the global variables:

```php
$request = RequestExtension::fromGlobal();
```

#### Retrieving Request Information

- `RequestExtension::url()`: Returns the URL of the request.
- `RequestExtension::method()`: Returns the HTTP method of the request.
- `RequestExtension::path()`: Returns the path of the request URI.
- `RequestExtension::inputs()`: Returns an object containing all input data from the request.
- `RequestExtension::input(string $input)`: Returns the value of a specific input parameter.
- `RequestExtension::validateInputs()`: Validates the input data using a third-party validation library.
- `RequestExtension::onlyInputs(array $inputs)`: Returns an object containing only the input data for the specified input keys.
- `RequestExtension::data(bool $associative = false)`: Returns the input data as an array or object.
- `RequestExtension::getTokenFromBearer()`: Extracts the token from the "Authorization" header (Bearer authentication).

### UriExtension

The `UriExtension` class extends the `Effectra\Http\Message\Uri` class and provides additional methods for working with URIs.

#### Modifying Queries

- `UriExtension::withQueries(array $queries)`: Returns a new URI object with the specified query parameters.

### ResponseExtension

The `ResponseExtension` class extends the `Effectra\Http\Message\Response` class and provides additional methods for working with HTTP responses.

#### Creating a JSON Response

- `ResponseExtension::json($data, int $status_code = 200, array $headers = [])`: Creates a JSON response with the specified data, status code, and additional headers.

#### Converting Response Body to Array

- `ResponseExtension::jsonToArray(?bool $associative = null, int $depth = 512, int $flags = 0)`: Converts the response body from JSON to an associative array.

#### File Attachment

- `ResponseExtension::attachFile(string $filePath, ?string $filename = null, ?string $contentType = null)`: Creates a response with a file attachment.

#### Setting Cookies

- `ResponseExtension::withCookie(string $name, string $value, int $expires = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true)`: Sets a cookie with the given parameters.
- `ResponseExtension::withCookies(array $cookies, int $expires = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true)`: Sets multiple cookies with the given parameters.

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvement, please open an issue or submit a pull request.

## License

This package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

Effectra\Http\Extensions is developed and maintained by [Mohammed Taha](https://github.com/BmtMohammedTaha).
