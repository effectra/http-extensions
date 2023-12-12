<?php

namespace Effectra\Http\Extensions;

use Effectra\Http\Extensions\Contracts\ResponseExtensionInterface;
use Effectra\Http\Message\Response;
use Effectra\Http\Message\Stream;
use JsonException;
use Psr\Http\Message\ResponseInterface;

class ResponseExtension extends Response implements ResponseExtensionInterface
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

}
