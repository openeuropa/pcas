<?php
namespace OpenEuropa\pcas;

use OpenEuropa\pcas\Cas\Protocol\CasProtocolInterface;
use OpenEuropa\pcas\Http\HttpClientInterface;
use OpenEuropa\pcas\Security\Core\User\PCasUser;
use OpenEuropa\pcas\Utils\GlobalVariablesGetter;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class PCas.
 */
class PCas implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * The HTTP client.
     *
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * The client properties.
     *
     * @var array
     */
    protected $properties;

    /**
     * The cache.
     *
     * @var CacheInterface
     */
    private $cache;

    /**
     * The authenticate variable.
     *
     * True when the user is authenticated, false otherwise.
     *
     * @var bool
     */
    private $authenticated = false;

    /**
     * The session.
     *
     * @var SessionInterface
     */
    private $session;

    /**
     * The CAS protocol.
     *
     * @var CasProtocolInterface
     */
    private $protocol;

    /**
     * PCas constructor.
     *
     * @param array $properties
     *   The properties.
     * @param \OpenEuropa\pcas\Http\HttpClientInterface $client
     * @param \OpenEuropa\pcas\Cas\Protocol\CasProtocolInterface $protocol
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Psr\SimpleCache\CacheInterface $cache
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(
        array $properties,
        HttpClientInterface $client,
        CasProtocolInterface $protocol,
        SessionInterface $session,
        CacheInterface $cache = null,
        LoggerInterface $logger = null
    ) {
        $this->setProperties($properties);

        $this->setHttpClient($client);
        $this->setProtocol(
            $protocol->withHttpClient(
                $this->getHttpClient()
            )
        );
        $this->setSession($session);

        if ($cache instanceof CacheInterface) {
            $this->setCache($cache);
        }

        // @todo: work on this.
        if ($logger instanceof LoggerInterface) {
            $this->setLogger($logger);
        }
    }

    /**
     * Get the CAS protocol object.
     *
     * @return CasProtocolInterface
     */
    public function getProtocol()
    {
        $this->protocol->setSession($this->getSession());
        $this->protocol->setProperties($this->getProperties());

        return $this->protocol;
    }

    /**
     * Set the CAS protocol object.
     *
     * @param \OpenEuropa\pcas\Cas\Protocol\CasProtocolInterface $protocol
     *
     * @return $this
     */
    public function setProtocol(CasProtocolInterface $protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Set the session.
     *
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface|null $session
     *
     * @return $this
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Returns the session namespace or the named session member.
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session|\Symfony\Component\HttpFoundation\Session\SessionInterface
     *   The session.
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set the cache.
     *
     * @param \Psr\SimpleCache\CacheInterface|null $cache
     *   The cache.
     *
     * @return $this
     */
    public function setCache(CacheInterface $cache = null)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get the cache.
     *
     * @return \Psr\SimpleCache\CacheInterface
     *   The cache.
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the properties.
     *
     * @param array $properties
     *   The properties.
     *
     * @return \OpenEuropa\pcas\PCas
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * Get the properties.
     *
     * @return array
     *   The properties.
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get the logger.
     *
     * @return LoggerInterface
     *   The logger.
     */
    public function getLogger()
    {
        if (null === $this->logger) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * Get the authenticated user object.
     *
     * @return PCasUser|null
     *   This contains the information of the user.
     *   Null is returned if the user has not been authenticated.
     */
    public function getAuthenticatedUser()
    {
        return $this->getSession()->get('pcas/user');
    }

    /**
     * Set a query parameter.
     *
     * @param string $name
     *   The query parameter's name.
     * @param string $value
     *   The query parameter's value.
     *
     * @return $this
     */
    public function setQueryParameter($name, $value)
    {
        $this->getSession()->set('pcas/query/' . $name, $value);

        return $this;
    }

    /**
     * Get a query parameter.
     *
     * @param string $name
     *   The query parameter.
     * @param null|mixed $default
     *
     * @return string
     *   The query parameter's value.
     */
    public function getQueryParameter($name, $default = null)
    {
        return $this->getSession()->get('pcas/query/' . $name, $default);
    }

    /**
     * Clear a query parameter.
     *
     * @param string $name
     *   The query parameter.
     *
     * @return mixed
     */
    public function clearQueryParameter($name)
    {
        return $this->getSession()->remove('pcas/query/' . $name);
    }

    /**
     * If not authenticated, redirect to CAS login.
     *
     * @param array $query
     *   The query parameters. If you want to redirect to a particular URL,
     *   set the key 'service'.
     *
     * @throws \Exception
     *
     * @return null|\Psr\Http\Message\ResponseInterface
     *   Null if user is already authenticated, the HTTP Response otherwise.
     */
    public function login(array $query = [])
    {
        $response = null;
        $this->clearQueryParameter('gateway');

        if (!$this->isAuthenticated()) {
            $response = $this->getHttpClient()->redirect($this->loginUrl($query));
        }

        return $response;
    }

    /**
     * The CAS login URL.
     *
     * @param array $query
     *   The query parameters.
     *
     * @return string|\Psr\Http\Message\UriInterface
     */
    public function loginUrl(array $query = [])
    {
        return $this->getProtocol()->get('login', $query);
    }

    /**
     * Forces client to re-enter credentials at CAS login.
     *
     * @param array $query
     *   The query parameters. If you want to redirect to a particular URL,
     *   set the key 'service'.
     *
     * @throws \Exception
     *
     * @return null|\Psr\Http\Message\ResponseInterface
     *   Null if user is already authenticated, the HTTP Response otherwise.
     */
    public function renewLogin(array $query = [])
    {
        $response = null;
        $was = ('false' === $this->getQueryParameter('renew', 'false')) ? false : true;

        if (!$this->isAuthenticated(!$was)) {
            $response = $this->getHttpClient()->redirect($this->loginUrl($query));
        }

        return $response;
    }

    /**
     * If client is not authenticated, attempt a gateway (transparent) CAS login.
     *
     * @param array $query
     *   The query parameters. If you want to redirect to a particular URL,
     *   set the key 'service'.
     *
     * @throws \Exception
     *
     * @return null|\Psr\Http\Message\ResponseInterface
     *   Null if user is already authenticated, the HTTP Response otherwise.
     */
    public function gatewayAuthentication(array $query = [])
    {
        $response = null;
        $query['gateway'] = 'true';

        $wasGateway = $this->getQueryParameter('gateway');
        $this->clearQueryParameter('gateway');

        if (!$wasGateway && !$this->isAuthenticated()) {
            $this->setQueryParameter('gateway', 'true');
            $response = $this->getHttpClient()->redirect($this->loginUrl($query));
        }

        return $response;
    }

    /**
     * Redirect to CAS logout.
     *
     * @param array $query
     *   The query parameters. If you want to redirect to a particular URL,
     *   set the key 'service'.
     *
     * @throws \Exception
     *
     * @return null|\Psr\Http\Message\ResponseInterface
     */
    public function logout(array $query = [])
    {
        $response = null;

        if ($this->isAuthenticated()) {
            if ($this->getSession()->invalidate()) {
                $response = $this->getHttpClient()->redirect($this->logoutUrl($query));
            }
        }

        return $response;
    }

    /**
     * The CAS logout URL.
     *
     * @param array $query
     *   The query parameters.
     *
     * @return string|\Psr\Http\Message\UriInterface
     */
    public function logoutUrl(array $query = [])
    {
        return $this->getProtocol()->get('logout', $query);
    }

    /**
     * Check if the user has been authenticated successfully.
     *
     * @param bool $renew
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function isAuthenticated($renew = false)
    {
        if (true === $renew) {
            $this->setQueryParameter('renew', 'true');
            $this->authenticated = false;
        } elseif (!$this->authenticated && $this->getAuthenticatedUser()) {
            $this->setQueryParameter('renew', 'false');
            $this->authenticated = true;
        }

        if (false === $this->authenticated && GlobalVariablesGetter::has('ticket')) {
            $this->validateServiceTicket();
        }

        return $this->authenticated;
    }

    /**
     * Validates the received service ticket.
     *
     * @param string|null $serviceTicket
     *   The ticket received from ECAS
     * @param bool     $requestPgt
     * @param string|null $url
     *
     * @throws \Exception
     *   When the validation of the ticket fails
     *
     * @return bool
     *   True if validated, false otherwise.
     */
    public function validateServiceTicket($serviceTicket = null, $requestPgt = false, $url = null)
    {
        if (null === $serviceTicket) {
            $serviceTicket = GlobalVariablesGetter::get('ticket');
        }

        if (null === $url) {
            $url = $this->getDefaultService();
        }

        $validated = $this->getProtocol()->validateServiceTicket($serviceTicket, $requestPgt, $url);

        if ($validated) {
            $this->authenticated = true;
            $this->getSession()->set('pcas/ticket', $serviceTicket);
        }

        return $validated;
    }

    /**
     * Get the HTTP client.
     *
     * @return \OpenEuropa\pcas\Http\HttpClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set the HTTP client.
     *
     * @param \OpenEuropa\pcas\Http\HttpClientInterface $httpClient
     *   The HTTP client.
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get the default service name.
     *
     * @param string $service
     *
     * @return string
     *   The default service name.
     */
    protected function getDefaultService($service = '')
    {
        return $this->getProtocol()->currentUrl($service)->__toString();
    }
}
