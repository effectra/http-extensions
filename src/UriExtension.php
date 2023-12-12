<?php

namespace Effectra\Http\Extensions;

use Effectra\Http\Message\Uri;
use Psr\Http\Message\UriInterface;

class UriExtensionInterface extends Uri  implements UriExtensionInterface
{
    /**
     * Returns a new Uri object with the specified query parameters.
     *
     * @param array $queries An array of query parameters.
     * @return Uri The new Uri object with the updated query parameters.
     */
    public function withQueries(array $queries): UriInterface
    {
        $clone = clone $this;
        $clone->withQuery(http_build_query($queries));
        return $clone;
    }

    /**
     * Get the query parameters as an array from the URI.
     *
     * @return array The array of query parameters.
     */
    public function getQueryParams(): array

    {
        $query = $this->getQuery();
        parse_str($query, $queryParams);
        return $queryParams;
    }

    /**
     * Returns a new Uri object with the specified path segments appended to the existing path.
     *
     * @param string ...$segments The path segments to append.
     * @return Uri The new Uri object with the updated path segments.
     */
    public function withPathSegments(string ...$segments): UriInterface
    {
        $path = $this->getPath();

        foreach ($segments as $segment) {
            $path = rtrim($path, '/') . '/' . ltrim($segment, '/');
        }

        $clone = clone $this;
        $clone = $clone->withPath($path);
        return $clone;
    }
}
