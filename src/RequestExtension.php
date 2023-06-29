<?php

namespace Effectra\Http\Extensions;

use Effectra\Http\Message\ServerRequest;

class RequestExtension extends ServerRequest
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
     * Returns an object containing all input data, including query params,
     * POST data, and input stream data.
     *
     * @return object An object of input data.
     */
    public function inputs(): object
    {
        // Get query params
        $getParams = $this->getQueryParams();

        // Get POST data
        $postParams = $this->getParsedBody();

        // Get input stream data
        $inputData = $this->parseJsonFromBody() ?? []; 

        // Merge all params into a single array
        $params = array_merge($getParams, $postParams, $inputData);

        return (object) $params;
    }

    /**
     * Returns the value of a specified input parameter.
     *
     * @param string $input The name of the input parameter.
     *
     * @return string|null The value of the input parameter, or null if it is not set.
     */
    public function input(string $input): ?string
    {
        $inputs = (array) self::inputs();
        return $inputs[$input] ?? null;
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
        return (object) array_intersect_key((array) $reqInputs, array_flip($inputs));
    }

    /**
     * Returns all input data.
     *
     * @param bool $associative Determines whether to return the input data as an associative array (true) or an object (false). Default is false.
     *
     * @return array|object The input data as an associative array or an object, based on the $associative parameter.
     */
    public function data(bool $associative = false): array|object
    {
        if ($associative) {
            return (array) $this->inputs();
        }
        return $this->inputs();
    }

    
}
