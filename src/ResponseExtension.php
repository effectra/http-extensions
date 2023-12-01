<?php

namespace Effectra\Http\Extensions;

use Effectra\Http\Message\Response;
use Effectra\Http\Message\Stream;
use JsonException;
use Psr\Http\Message\ResponseInterface;

class ResponseExtension extends Response
{
    use MessageExtensionTrait;

     /**
     * Creates a redirect response.
     *
     * @param string $url The URL to redirect to.
     * @param int $statusCode The HTTP status code for the redirect response. Default is 302 (Found).
     * @return ResponseInterface
     */
    public function redirect(string $url, int $statusCode = 302): ResponseInterface
    {
        /** @var ResponseInterface $clone */
        $clone = clone $this;

        return $clone
            ->withStatus($statusCode)
            ->withHeader('location', $url);
    }

    /**
     * Creates a JSON response.
     *
     * @param mixed $data The data to be encoded as JSON.
     * @param int $status_code The HTTP status code for the response. Default is 200 (OK).
     * @param array $headers Additional headers to be added to the response.
     * @return ResponseInterface
     */
    public function json($data, int $status_code = 200, array $headers = []): ResponseInterface
    {
        /** @var ResponseInterface $clone */
        $clone = clone $this;

        foreach ($headers as $header) {
            $clone->withHeader(array_keys($header)[0], array_values($header)[0]);
        }

        return $clone
            ->withStatus($status_code)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(new Stream(json_encode((array) $data)));
    }

    /**
     * Converts the response body from JSON to an associative array.
     *
     * @param bool|null $associative Determines whether to return an associative array. Default is null.
     * @param int $depth The recursion depth. Default is 512.
     * @param int $flags Bitmask of JSON decode options. Default is 0.
     * @return array|string The JSON-decoded response body.
     * @throws \Exception If the response body cannot be decoded.
     */
    public function jsonToArray(?bool $associative = null, int $depth = 512, int $flags = 0): string
    {
        try {
            $body = $this->getBody()->getContents();
            return json_decode($body, $associative, $depth, $flags);
        } catch (JsonException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Retrieves the reason phrase for a given HTTP status code.
     *
     * @param mixed $statusCode The HTTP status code.
     * @return string The reason phrase corresponding to the status code, or an empty string if not found.
     */
    public static function parseReasonPhrase($statusCode): string
    {
        return static::$statusTexts[(int) $statusCode] ?? '';
    }

    /**
     * Creates a response to initiate a file download.
     *
     * @param string $filePath The path to the file to be downloaded.
     * @param string|null $filename The filename to be used for the downloaded file. If not provided, the original file name will be used.
     * @param string|null $contentType The content type of the file. If not provided, it will be inferred from the file extension.
     * @return ResponseInterface
     */
    public function download(string $filePath, ?string $filename = null, ?string $contentType = null): ResponseInterface
    {
        if (!is_readable($filePath)) {
            throw new \RuntimeException("File '{$filePath}' cannot be read.");
        }

        $fileStream = new Stream(fopen($filePath, 'rb'));

        $filename = $filename ?? basename($filePath);
        $contentType = $contentType ?? mime_content_type($filePath);

        /** @var ResponseInterface $clone */
        $clone = clone $this;

        return $clone
            ->withBody($fileStream)
            ->withHeader('Content-Type', $contentType)
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Content-Length', (string) $fileStream->getSize());
    }

    /**
     * Reads a file and returns it as a response with the appropriate content type.
     *
     * @param string $filePath The path to the file to be read.
     * @return ResponseInterface
     */
    public function readFile(string $filePath): ResponseInterface
    {
        if (!is_readable($filePath)) {
            throw new \RuntimeException("File '{$filePath}' cannot be read.");
        }

        $fileStream = new Stream(fopen($filePath, 'rb'));
        $contentType = mime_content_type($filePath);

        /** @var ResponseInterface $clone */
        $clone = clone $this;

        return $clone
            ->withBody($fileStream)
            ->withHeader('Content-Type', $contentType)
            ->withHeader('Content-Length', (string) $fileStream->getSize());
    }

    /**
     * Creates a response with a file attachment.
     *
     * @param string $filePath The path to the file to be attached.
     * @param string|null $filename The name to be used for the file attachment. If not provided, the original file name will be used.
     * @param string|null $contentType The content type of the file attachment. If not provided, the content type will be inferred from the file extension.
     * @return ResponseInterface
     */
    public function attachFile(string $filePath, ?string $filename = null, ?string $contentType = null): ResponseInterface
    {
        if (!is_readable($filePath)) {
            throw new \RuntimeException("File '{$filePath}' cannot be read.");
        }

        $fileStream = new Stream(fopen($filePath, 'rb'));

        // Set the filename if provided, or use the original file name
        $filename = $filename ?? basename($filePath);

        // Infer the content type if not provided
        $contentType = $contentType ?? mime_content_type($filePath);

        /** @var ResponseInterface $clone */
        $clone = clone $this;

        return $clone
            ->withBody($fileStream)
            ->withHeader('Content-Type', $contentType)
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Content-Length', (string) $fileStream->getSize());
    }

    /**
     * Sets a cookie with the given parameters.
     *
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @param int $expires The expiration time of the cookie in Unix timestamp format. Default is 0 (session cookie).
     * @param string $path The path on the server where the cookie will be available. Default is '/' (all paths).
     * @param string $domain The domain that the cookie is available to. Default is an empty string (current domain).
     * @param bool $secure Indicates if the cookie should only be transmitted over secure HTTPS connections. Default is false.
     * @param bool $httpOnly Indicates if the cookie should only be accessible through HTTP(S) and not JavaScript. Default is true.
     * @return ResponseInterface
     */
    public function withCookie(string $name, string $value, int $expires = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true): ResponseInterface
    {
        return $this->withCookies([$name => $value], $expires, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Sets multiple cookies with the given parameters.
     *
     * @param array $cookies An associative array of cookies where the keys are the cookie names and the values are the cookie values.
     * @param int $expires The expiration time of the cookies in Unix timestamp format. Default is 0 (session cookies).
     * @param string $path The path on the server where the cookies will be available. Default is '/' (all paths).
     * @param string $domain The domain that the cookies are available to. Default is an empty string (current domain).
     * @param bool $secure Indicates if the cookies should only be transmitted over secure HTTPS connections. Default is false.
     * @param bool $httpOnly Indicates if the cookies should only be accessible through HTTP(S) and not JavaScript. Default is true.
     * @return ResponseInterface
     */
    public function withCookies(array $cookies, int $expires = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true): ResponseInterface
    {
        $cookieStrings = [];
        foreach ($cookies as $name => $value) {
            $cookieStrings[] = urlencode($name) . '=' . urlencode($value);
        }

        $cookieHeader = implode('; ', $cookieStrings);
        if ($expires > 0) {
            $expiresFormatted = gmdate('D, d M Y H:i:s T', $expires);
            $cookieHeader .= "; expires={$expiresFormatted}";
        }
        if (!empty($path)) {
            $cookieHeader .= "; path={$path}";
        }
        if (!empty($domain)) {
            $cookieHeader .= "; domain={$domain}";
        }
        if ($secure) {
            $cookieHeader .= '; secure';
        }
        if ($httpOnly) {
            $cookieHeader .= '; HttpOnly';
        }

        /** @var ResponseInterface $clone */
        $clone = clone $this;

        return $clone->withAddedHeader('Set-Cookie', $cookieHeader);
    }

}
