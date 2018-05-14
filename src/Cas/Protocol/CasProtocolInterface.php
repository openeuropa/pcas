<?php
namespace OpenEuropa\pcas\Cas\Protocol;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * Get the http client.
     *
     * @return \OpenEuropa\pcas\Http\HttpClientInterface
     *   The http client.
     */
    public function getHttpClient();

    /**
     * Get the URI factory.
     *
     * @return \Http\Message\UriFactory|NULL
     */
    public function getUriFactory();

    /**
     * Get the library properties.
     *
     * @return array
     *   The properties.
     */
    public function getProperties();

    /**
     * Set the session.
     *
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     *
     * @return mixed
     */
    public function setSession(SessionInterface $session);

    /**
     * Set properties.
     *
     * @param array $properties
     *
     * @return mixed
     */
    public function setProperties(array $properties);
}
