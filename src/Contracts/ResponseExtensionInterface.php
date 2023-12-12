<?php

namespace Effectra\Http\Extensions\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ResponseExtensionInterface extends ResponseInterface,MessageExtensionInterface
{

     /**
     * Creates a redirect response.
     *
     * @param string $url The URL to redirect to.
     * @param int $statusCode The HTTP status code for the redirect response. Default is 302 (Found).
     * @return ResponseInterface
     */
    public function redirect(string $url, int $statusCode = 302): ResponseInterface;

    /**
     * Creates a JSON response.
     *
     * @param mixed $data The data to be encoded as JSON.
     * @param int $status_code The HTTP status code for the response. Default is 200 (OK).
     * @param array $headers Additional headers to be added to the response.
     * @return ResponseInterface
     */
    public function json($data, int $status_code = 200, array $headers = []): ResponseInterface;
    /**
     * Converts the response body from JSON to an associative array.
     *
     * @param bool|null $associative Determines whether to return an associative array. Default is null.
     * @param int $depth The recursion depth. Default is 512.
     * @param int $flags Bitmask of JSON decode options. Default is 0.
     * @return array|string The JSON-decoded response body.
     * @throws \Exception If the response body cannot be decoded.
     */
    public function jsonToArray(?bool $associative = null, int $depth = 512, int $flags = 0): string;

    /**
     * Retrieves the reason phrase for a given HTTP status code.
     *
     * @param mixed $statusCode The HTTP status code.
     * @return string The reason phrase corresponding to the status code, or an empty string if not found.
     */
    public static function parseReasonPhrase($statusCode): string;

    /*
     * Creates a response to initiate a file download.
     *
     * @param string $filePath The path to the file to be downloaded.
     * @param string|null $filename The filename to be used for the downloaded file. If not provided, the original file name will be used.
     * @param string|null $contentType The content type of the file. If not provided, it will be inferred from the file extension.
     * @return ResponseInterface
     */
    public function download(string $filePath, ?string $filename = null, ?string $contentType = null): ResponseInterface;

    /**
     * Reads a file and returns it as a response with the appropriate content type.
     *
     * @param string $filePath The path to the file to be read.
     * @return ResponseInterface
     */
    public function readFile(string $filePath): ResponseInterface;

    /**
     * Creates a response with a file attachment.
     *
     * @param string $filePath The path to the file to be attached.
     * @param string|null $filename The name to be used for the file attachment. If not provided, the original file name will be used.
     * @param string|null $contentType The content type of the file attachment. If not provided, the content type will be inferred from the file extension.
     * @return ResponseInterface
     */
    public function attachFile(string $filePath, ?string $filename = null, ?string $contentType = null): ResponseInterface;

}
