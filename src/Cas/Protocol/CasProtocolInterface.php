<?php
namespace OpenEuropa\pcas\Cas\Protocol;

interface CasProtocolInterface
{
    /**
     * Get a specific URL.
     *
     * @param string $path
     *   The protocol endpoint. Could be login, logout, servicevalidate, etc.
     *   They are defined in config.yml.
     * @param array $query
     *   The query parameters to add to the query.
     *
     * @return string|\Psr\Http\Message\UriInterface
     *   The URI.
     */
    public function get($path, array $query = []);

    /**
     * Get the current URL.
     *
     * @param string $url
     *   The URL.
     *
     * @return \Psr\Http\Message\UriInterface
     *   The URI.
     */
    public function currentUrl($url = '');

    /**
     * Get the container.
     *
     * @return \Psr\Container\ContainerInterface
     *   The container.
     */
    public function getContainer();

    /**
     * Get the library properties.
     *
     * @return array
     *   The properties.
     */
    public function getProperties();
}
