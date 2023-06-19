<?php

namespace Effectra\Http\Extensions;

use Effectra\Http\Foundation\RequestFoundation;
use Effectra\Http\Message\ServerRequest;
use Effectra\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface;

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
     * Creates a new Request object from the global request.
     *
     * @return ServerRequestInterface  The Server Request object.
     */
    public static function fromGlobal(): ServerRequestInterface 
    {
        return RequestFoundation::createFromGlobals();
    }

    /**
     * Returns the URL of the current request.
     *
     * @return string The URL of the current request.
     */
    public static function url(): string
    {
        return static::fromGlobal()->getUri();
    }

    /**
     * Returns the HTTP method of the current request.
     *
     * @return string The HTTP method of the current request.
     */
    public static function method(): string
    {
        return static::fromGlobal()->getMethod();
    }

    /**
     * Returns the path of the current request URI.
     *
     * @return string The path of the current request URI.
     */
    public static function path(): string
    {
       return static::fromGlobal()->getUri()->getPath();
    }

    /**
     * Returns an object containing all input data, including query params,
     * POST data, and input stream data.
     *
     * @return object An object of input data.
     */
    public static function inputs(): object
    {
        $request = static::fromGlobal();

        // Get query params
        $getParams = $request->getQueryParams();

        // Get POST data
        $postParams = $request->getParsedBody();

        // Get input stream data
        $inputData = static::parseJsonFromBody($request) ?? [];

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
    public static function input(string $input): ?string
    {
        $inputs = (array) self::inputs();
        return $inputs[$input] ?? null;
    }

    /**
     * Validates input data using a third-party validation library.
     *
     * @return Validator A Validator object representing the validation results.
     */
    public function validateInputs(): Validator
    {
        $data = self::inputs();
        $v = new Validator($data);
        return $v;
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
        $reqInputs = self::inputs();
        return (object) array_intersect_key((array) $reqInputs, array_flip($inputs));
    }

    /**
     * Returns all input data.
     *
     * @param bool $associative Determines whether to return the input data as an associative array (true) or an object (false). Default is false.
     *
     * @return array|object The input data as an associative array or an object, based on the $associative parameter.
     */
    public static function data(bool $associative = false): array|object
    {
        if ($associative) {
            return (array) self::inputs();
        }
        return self::inputs();
    }

    /**
     * Retrieves the token from the Authorization header using the Bearer scheme.
     *
     * @return string|null The token from the Authorization header, or null if it is not set.
     */
    public static function getTokenFromBearer(): ?string
    {
        $header = static::fromGlobal()->getHeaderLine('Authorization');
        if ($header) {
            preg_match('/Bearer\s+(.*)$/i', $header, $matches);
            return $matches[1] ?? null;
        }
        return null;
    }
}
