<?php

namespace Effectra\Http\Extensions;

use Effectra\Http\Message\Uri;

class UriExtension extends Uri
{
    /**
     * Returns a new Uri object with the specified query parameters.
     *
     * @param array $queries An array of query parameters.
     * @return Uri The new Uri object with the updated query parameters.
     */
    public function withQueries(array $queries): Uri
    {
        $clone = clone $this;
        $clone->withQuery(http_build_query($queries));
        return $clone;
    }
}
