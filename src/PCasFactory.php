<?php
namespace OpenEuropa\pcas;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class PCasFactory.
 */
class PCasFactory
{

    /**
     * The service container.
     *
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * PCasFactory constructor.
     *
     * @param SessionInterface $session
     * @param string $baseUrl
     * @param array $protocol
     *
     * @throws \Exception
     */
    public function __construct(SessionInterface $session, string $baseUrl = '', array $protocol = [])
    {
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('p_cas.yml');
        $this->container->set('session', $session);

        $configuration = $this->container->getParameter('p_cas');
        if (!empty($baseUrl)) {
            $configuration['base_url'] = $baseUrl;
        }
        if (!empty($protocols)) {
            $configuration['protocol'] = $protocol;
        }
        $this->container->setParameter('p_cas', $configuration);

        $this->container->compile();
    }

    /**
     * Returns the default parameters as set in the default yaml config file.
     *
     * @return array
     */
    public static function getDefaultParameters()
    {
        $values = Yaml::parseFile(__DIR__ . '/../Resources/config/p_cas.yml');

        return $values['parameters']['p_cas'];
    }

    /**
     * @throws InvalidArgumentException
     *  When no definitions are available
     * @throws ServiceCircularReferenceException
     *  When a circular reference is detected
     * @throws ServiceNotFoundException
     *  When the service is not defined
     * @throws \Exception
     *
     * @return \OpenEuropa\pcas\pcas
     */
    public function getPCas()
    {
        return $this->container->get('pcas');
    }

    /**
     * @throws InvalidArgumentException
     *  When no definitions are available
     * @throws ServiceCircularReferenceException
     *  When a circular reference is detected
     * @throws ServiceNotFoundException
     *  When the service is not defined
     * @throws \Exception
     *
     * @return \OpenEuropa\pcas\Http\Client
     */
    public function getHttpClient()
    {
        return $this->container->get('pcas.httpclient');
    }

    /**
     * @throws InvalidArgumentException
     *  When no definitions are available
     * @throws ServiceCircularReferenceException
     *  When a circular reference is detected
     * @throws ServiceNotFoundException
     *  When the service is not defined
     * @throws \Exception
     *
     * @return \OpenEuropa\pcas\Cas\Protocol\V2\CasProtocolV2
     */
    public function getProtocol()
    {
        return $this->container->get('pcas.protocol');
    }

    /**
     * @throws InvalidArgumentException
     *  When no definitions are available
     * @throws ServiceCircularReferenceException
     *  When a circular reference is detected
     * @throws ServiceNotFoundException
     *  When the service is not defined
     * @throws \Exception
     *
     * @return \OpenEuropa\pcas\Security\Core\User\PCasUserFactory
     */
    public function getUserFactory()
    {
        return $this->container->get('pcas.userfactory');
    }

    /**
     * @throws InvalidArgumentException
     *  When no definitions are available
     * @throws ServiceCircularReferenceException
     *  When a circular reference is detected
     * @throws ServiceNotFoundException
     *  When the service is not defined
     * @throws \Exception
     *
     * @return \OpenEuropa\pcas\Utils\PCasSerializerFactory
     */
    public function getSerializerFactory()
    {
        return $this->container->get('pcas.serializerfactory');
    }

    /**
     * @throws InvalidArgumentException
     *  When no definitions are available
     * @throws ServiceCircularReferenceException
     *  When a circular reference is detected
     * @throws ServiceNotFoundException
     *  When the service is not defined
     * @throws \Exception
     *
     * @return \OpenEuropa\pcas\Http\HttpClientFactory
     */
    public function getHttpClientFactory()
    {
        return $this->container->get('pcas.httpclientfactory');
    }

    /**
     * Return session object.
     *
     * @throws \Exception
     *
     * @return \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    public function getSession()
    {
        return $this->container->get('session');
    }
}
