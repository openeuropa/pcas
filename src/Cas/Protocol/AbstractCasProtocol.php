<?php
namespace OpenEuropa\pcas\Cas\Protocol;

use Http\Discovery\UriFactoryDiscovery;
use Http\Message\UriFactory;
use OpenEuropa\pcas\Http\HttpClientInterface;
use OpenEuropa\pcas\Security\Core\User\PCasUserFactoryInterface;
use OpenEuropa\pcas\Utils\PCasSerializerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class AbstractCasProtocol.
 */
abstract class AbstractCasProtocol implements CasProtocolInterface
{

    /**
     * The user factory.
     *
     * @var \OpenEuropa\pcas\Security\Core\User\PCasUserFactoryInterface
     */
    protected $userFactory;

    /**
     * The serializer factory.
     *
     * @var \OpenEuropa\pcas\Utils\PCasSerializerFactoryInterface
     */
    protected $serializerFactory;

    /**
     * The protocol properties.
     *
     * @var mixed[]
     */
    protected $properties;
    /**
     * The URI factory.
     *
     * @var \Http\Message\UriFactory
     */
    private $uriFactory;

    /**
     * The session.
     *
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

    /**
     * The HTTP client.
     *
     * @var \OpenEuropa\pcas\Http\HttpClientInterface
     */
    private $client;

    /**
     * AbstractCasProtocol constructor.
     *
     * @param \OpenEuropa\pcas\Security\Core\User\PCasUserFactoryInterface $PCasUserFactory
     * @param \OpenEuropa\pcas\Utils\PCasSerializerFactoryInterface        $serializerFactory
     * @param \Http\Message\UriFactory|NULL                                $uriFactory
     */
    public function __construct(
        PCasUserFactoryInterface $PCasUserFactory,
        PCasSerializerFactoryInterface $serializerFactory,
        UriFactory $uriFactory = null
    ) {
        $this->userFactory = $PCasUserFactory;
        $this->serializerFactory = $serializerFactory;
        $this->uriFactory = $uriFactory ?
            $uriFactory :
            UriFactoryDiscovery::find();
    }

    /**
     * @param \OpenEuropa\pcas\Http\HttpClientInterface $httpClient
     *
     * @return \OpenEuropa\pcas\Cas\Protocol\AbstractCasProtocol
     */
    public function withHttpClient(HttpClientInterface $httpClient)
    {
        $clone = clone $this;
        $clone->client = $httpClient;

        return $clone;
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
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpClient()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function getUriFactory()
    {
        return $this->uriFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function currentUrl($url = '')
    {
        if (empty($url)) {
            $request = Request::createFromGlobals();
            $request->getQueryString();

            $url = $request->getSchemeAndHttpHost().$request->getRequestUri();
        }

        $uri = $this->uriFactory->createUri($url);

        // Remove the ticket parameter if any.
        parse_str($uri->getQuery(), $query);
        unset($query['ticket']);

        return $uri->withQuery(http_build_query($query));
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, array $query = [])
    {
        $properties = $this->getProperties();
        $name = strtolower($name);

        $properties += [
            'protocol' => [
                $name => [],
            ],
        ];

        $properties['protocol'][$name] += [
            'query' => [],
            'allowed_parameters' => [],
        ];

        $query += $properties['protocol'][$name]['query'];
        $query += ['service' => ''];
        $query['service'] = $this->currentUrl($query['service'])->__toString();
        $query += (array) $this->getSession()->get('pcas/query');

        // Make sure that every query parameters is a string.
        $query = array_map(function ($value) {
            if (\is_array($value)) {
                $value = implode(
                    ',',
                    iterator_to_array(
                        new \RecursiveIteratorIterator(
                            new \RecursiveArrayIterator($value)
                        )
                    )
                );
            }

            return $value;
        }, $query);


        // Remove parameters that are not allowed.
        $query = array_intersect_key(
            $query,
            array_combine(
                $properties['protocol'][$name]['allowed_parameters'],
                $properties['protocol'][$name]['allowed_parameters']
            )
        );

        $uri = $this->getUriFactory()
            ->createUri($properties['base_url'])
            ->withQuery(http_build_query($query));

        return $uri->withPath($uri->getPath() . $properties['protocol'][$name]['path']);
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }
}
