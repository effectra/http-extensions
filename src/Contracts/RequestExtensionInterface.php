<?php

namespace Effectra\Http\Extensions\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface RequestExtensionInterface extends ServerRequestInterface,MessageExtensionInterface
{

    /**
     * Returns the URL of the current request.
     *
     * @return string The URL of the current request.
     */
    public function url(): string;

    /**
     * Returns the HTTP method of the current request.
     *
     * @return string The HTTP method of the current request.
     */
    public function method(): string;

    /**
     * Returns the path of the current request URI.
     *
     * @return string The path of the current request URI.
     */
    public function path(): string;

    /**
     * Returns an array containing all input data, including query params,
     * POST data, and input stream data.
     *
     * @return array An array of input data.
     */
    public function inputs(): array;

    /**
     * Returns an object containing all input data, including query params,
     * POST data, and input stream data.
     *
     * @return object An object of input data.
     */
    public function inputsAsObject();

    /**
     * Returns the value of a specified input parameter.
     *
     * @param string $input The name of the input parameter.
     *
     * @return mixed The value of the input parameter, or default if it is not set.
     */
    public function input(string $input,$default = null): mixed;

    /**
     * Returns an object containing only the input data for the specified input keys.
     *
     * @param array $inputs An array of input keys to include in the result.
     *
     * @return object An object containing only the input data for the specified input keys.
     */
    public function onlyInputs(array $inputs): object;

    /**
     * Returns all input data.
     *
     * @param bool $associative Determines whether to return the input data as an associative array (true) or an object (false). Default is false.
     *
     * @return array|object The input data as an associative array or an object, based on the $associative parameter.
     */
    public function data(bool $associative = true): array|object;

    /**
     * Get the client's IP address taking into account trusted proxies.
     *
     * This function retrieves the client's IP address by considering trusted proxy servers
     * that might be forwarding requests. It checks if the remote address is one of the trusted
     * proxies and if the X-Forwarded-For header is present. If so, it returns the first non-empty
     * IP address from the list of forwarded addresses. Otherwise, it returns the remote address.
     *
     * @param array $trustedProxies An array of trusted proxy IP addresses.
     * @return string|null The client's IP address or null if not available.
     */
    public function getClientIp(array $trustedProxies): ?string;

}
