<?php

namespace OpenEuropa\pcas\Http;

use Http\Client\HttpClient;

interface PCasHttpClientInterface extends HttpClient
{
    /**
     * Do a redirection.
     *
     * @param \Psr\Http\Message\UriInterface|string $url
     *   The URL to redirect to.
     * @param bool                               $replace
     *   The optional replace parameter indicates
     *   whether the header should replace a previous similar header, or
     *   add a second header of the same type. By default it will replace,
     *   but if you pass in false as the second argument you can force
     *   multiple headers of the same type.
     * @param int                               $code
     *   Forces the HTTP response code to the specified value.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *   The response.
     */
    public function redirect($url, $replace = true, $code = 302);
}
