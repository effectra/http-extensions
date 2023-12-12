<?php

namespace Effectra\Http\Extensions\Contracts;

use Effectra\Http\Message\Uri;
use Psr\Http\Message\UriInterface;

interface UriExtensionInterface extends UriInterface
{
    /**
     * Returns a new Uri object with the specified query parameters.
     *
     * @param array $queries An array of query parameters.
     * @return UriInterface The new Uri object with the updated query parameters.
     */
    public function withQueries(array $queries): UriInterface;

     /**
     * Get the query parameters as an array from the URI.
     *
     * @return array The array of query parameters.
     */
    public function getQueryParams(): array;

    /**
     * Returns a new Uri object with the specified path segments appended to the existing path.
     *
     * @param string ...$segments The path segments to append.
     * @return Uri The new Uri object with the updated path segments.
     */
    public function withPathSegments(string ...$segments): UriInterface;

}
