<?php

namespace Effectra\Http\Extensions;

use Effectra\Http\Message\Stream;
use Psr\Http\Message\ResponseInterface;

trait MessageExtensionTrait
{

    /**
     * Writes the response body.
     *
     * @param mixed $body The response body.
     * @return $this
     */
    public function write($body)
    {
        return $this->withBody($body);
    }

    /**
     * return no content response .
     *
     * @return $this
     */
    public function noContent()
    {
        return $this->write('')->withStatus(204);
    }

    /**
     * return Internal Error response .
     *
     * @return $this
     */
    public function serverError($message = 'Internal Error')
    {
        return $this->write($message)->withStatus(500);
    }

    /**
     * Adds a cookie to the response.
     *
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @param int $expires The expiration time of the cookie in Unix timestamp format. Default is 0 (session cookie).
     * @param string $path The path on the server where the cookie will be available. Default is '/' (all paths).
     * @param string $domain The domain that the cookie is available to. Default is an empty string (current domain).
     * @param bool $secure Indicates if the cookie should only be transmitted over secure HTTPS connections. Default is false.
     * @param bool $httpOnly Indicates if the cookie should only be accessible through HTTP(S) and not JavaScript. Default is true.
     *
     * @return self Returns a new instance of the response with the added cookie.
     */
    public function withCookies($name, $value, $expires = 0, $path = '/', $domain = '', $secure = false, $httpOnly = true)
    {
        $cookie = sprintf('%s=%s', $name, urlencode($value));

        if ($expires !== 0) {
            $cookie .= '; Expires=' . gmdate('D, d M Y H:i:s T', $expires);
        }

        if (!empty($path)) {
            $cookie .= '; Path=' . $path;
        }

        if (!empty($domain)) {
            $cookie .= '; Domain=' . $domain;
        }

        if ($secure) {
            $cookie .= '; Secure';
        }

        if ($httpOnly) {
            $cookie .= '; HttpOnly';
        }

        $clone = clone $this;
        $clone->withAddedHeader('Set-Cookie', $cookie);
        return $clone;
    }

    /**
     * Sets the response body as plain text.
     *
     * @param string $text The text to set as the response body.
     * @return ResponseInterface
     */
    public function text(string $text): ResponseInterface
    {
        /** @var ResponseInterface $clone */
        $clone = clone $this;

        return $clone->withBody(new Stream($text));
    }

    /**
     * Retrieves the token from the Authorization header of the request.
     *
     * @return string|null The token from the Authorization header, or null if not present or invalid.
     */
    public function getTokenFromAuthorizationHeader(): ?string
    {
        $authorizationHeader = $this->getHeaderLine('Authorization');
        return $authorizationHeader;
    }

    /**
     * Sets the token in the Authorization header of the response.
     *
     * @param string $token The token to set in the Authorization header.
     * @return ResponseInterface The response object with the updated Authorization header.
     */
    public function withBearerTokenHeader( string $token): ResponseInterface
    {
        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }

    /**
     * Parses the JSON body of the request.
     *
     * @return array|null The parsed JSON data, or null if the Content-Type is not 'application/json' or there was a parsing error.
     */
    public function parseJsonFromBody(): ?array
    {
        $body = $this->getBody()->getContents();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $data;
    }

    /**
     * Get the allowed HTTP methods from the 'Allow' header in the PSR-7 request.
     *
     * @return array The array of allowed HTTP methods.
     */
    function getAllowedMethods(): array
    {
        $allowedMethods = [];

        $header = $this->getHeader('Allow');
        if (!empty($header)) {
            $allowedMethods = array_map('trim', explode(',', $header[0]));
        }

        return $allowedMethods;
    }

    /**
     * Retrieves the token from the Authorization header using the Bearer scheme.
     *
     * @return string|null The token from the Authorization header, or null if it is not set.
     */
    public function getTokenFromBearer(): ?string
    {
        $token = null;
        $header = $this->getHeaderLine('Authorization');
        if ($header) {
            preg_match('/Bearer\s+(.*)$/i', $header, $matches);
            $token = $matches[1] ?? null;
        }
        return $token;
    }
}
