<?php

namespace Effectra\Http\Extensions;

use Effectra\Http\Extensions\Contracts\RequestExtensionInterface;
use Effectra\Http\Message\ServerRequest;

class RequestExtension extends ServerRequest implements RequestExtensionInterface
{
    use MessageExtensionTrait;

    public const METHOD_HEAD = 'HEAD';
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PURGE = 'PURGE';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_TRACE = 'TRACE';
    public const METHOD_CONNECT = 'CONNECT';

    /**
     * Returns the URL of the current request.
     *
     * @return string The URL of the current request.
     */
    public function url(): string
    {
        return $this->getUri();
    }

    /**
     * Returns the HTTP method of the current request.
     *
     * @return string The HTTP method of the current request.
     */
    public function method(): string
    {
        return $this->getMethod();
    }

    /**
     * Returns the path of the current request URI.
     *
     * @return string The path of the current request URI.
     */
    public function path(): string
    {
        return $this->getUri()->getPath();
    }

    /**
     * Returns an array containing all input data, including query params,
     * POST data, and input stream data.
     *
     * @return array An array of input data.
     */
    public function inputs(): array
    {
        // Get query params
        $getParams = $this->getQueryParams();

        // Get POST data
        $postParams = $this->getParsedBody();

        // Get input stream data
        $inputData = $this->parseJsonFromBody() ?? [];

        // Merge all params into a single array
        $params = array_merge($getParams, $postParams, $inputData);

        return $params;
    }

    /**
     * Returns an object containing all input data, including query params,
     * POST data, and input stream data.
     *
     * @return object An object of input data.
     */
    public function inputsAsObject():object
    {
        return (object) $this->inputs();
    }

    /**
     * Returns the value of a specified input parameter.
     *
     * @param string $input The name of the input parameter.
     *
     * @return mixed The value of the input parameter, or default if it is not set.
     */
    public function input(string $input,$default = null): mixed
    {
        $inputs = $this->inputs();
        return $inputs[$input] ?? $default;
    }

    /**
     * Returns an object containing only the input data for the specified input keys.
     *
     * @param array $inputs An array of input keys to include in the result.
     *
     * @return object An object containing only the input data for the specified input keys.
     */
    public function onlyInputs(array $inputs): object
    {
        $reqInputs = $this->inputs();
        return (object) array_intersect_key($reqInputs, array_flip($inputs));
    }

    /**
     * Returns all input data.
     *
     * @param bool $associative Determines whether to return the input data as an associative array (true) or an object (false). Default is false.
     *
     * @return array|object The input data as an associative array or an object, based on the $associative parameter.
     */
    public function data(bool $associative = true): array|object
    {
        if ($associative) {
            return (array) $this->inputs();
        }
        return $this->inputs();
    }

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
    public function getClientIp(array $trustedProxies): ?string
    {
        $serverParams = $this->getServerParams();
        $remoteAddress = $serverParams['REMOTE_ADDR'] ?? null;

        if (in_array($remoteAddress, $trustedProxies, true) && isset($serverParams['HTTP_X_FORWARDED_FOR'])) {
            $forwardedFor = explode(',', $serverParams['HTTP_X_FORWARDED_FOR']);
            foreach ($forwardedFor as $ip) {
                $ip = trim($ip);
                if ($ip !== '') {
                    return $ip;
                }
            }
        }

        return $remoteAddress;
    }
}
