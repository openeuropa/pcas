<?php
namespace OpenEuropa\pcas\Cas\Protocol;

use Http\Discovery\UriFactoryDiscovery;
use Http\Message\UriFactory;
use OpenEuropa\pcas\Security\Core\User\PCasUserFactoryInterface;
use OpenEuropa\pcas\Utils\PCasSerializerFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class AbstractCasProtocol.
 */
abstract class AbstractCasProtocol implements CasProtocolInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * The URI factory.
     *
     * @var \Http\Message\UriFactory
     */
    protected $uriFactory;

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
     * The session.
     *
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    protected $properties;

    /**
     * AbstractCasProtocol constructor.
     *
     * @param \OpenEuropa\pcas\Security\Core\User\PCasUserFactoryInterface $PCasUserFactory
     *   The user factory.
     * @param \OpenEuropa\pcas\Utils\PCasSerializerFactoryInterface $serializerFactory
     *   The serializer factory.
     * @param \Http\Message\UriFactory|NULL $uriFactory
     *   The URI factory.
     */
    public function __construct(
        PCasUserFactoryInterface $PCasUserFactory,
        PCasSerializerFactoryInterface $serializerFactory,
        UriFactory $uriFactory = null
    ) {
        $this->uriFactory = $uriFactory ?? UriFactoryDiscovery::find();
        $this->userFactory = $PCasUserFactory;
        $this->serializerFactory = $serializerFactory;
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
     * Get the container.
     *
     * @return \Psr\Container\ContainerInterface
     *   The container.
     */
    public function getContainer()
    {
        return $this->container;
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
    public function currentUrl($url = '')
    {
        if (empty($url)) {
            $request = Request::createFromGlobals();
            $request->getQueryString();

            $url = $request->getSchemeAndHttpHost() . $request->getRequestUri();
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

        return $this->uriFactory->createUri($properties['protocol'][$name]['uri'])
          ->withQuery(http_build_query($query));
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }
}
