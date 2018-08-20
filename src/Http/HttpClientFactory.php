<?php

namespace OpenEuropa\pcas\Http;

use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClientFactory;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\MessageFactory;
use Http\Message\UriFactory;

/**
 * Class HttpClientFactory.
 */
class HttpClientFactory implements HttpClientFactoryInterface
{
    /**
     * The URI Factory.
     *
     * @var \Http\Message\UriFactory
     */
    private $uriFactory;

    /**
     * The HTTP Client.
     *
     * @var \Http\Client\HttpClient
     */
    private $httpClient;

    /**
     * The message factory.
     *
     * @var \Http\Message\MessageFactory
     */
    private $messageFactory;

    /**
     * True if we should create a new Plugin client at next request.
     *
     * @var bool
     */
    private $httpClientModified = true;

    /**
     * @var \Http\Client\Common\Plugin[]
     */
    private $plugins = [];

    /**
     * HTTP Client factory constructor.
     *
     * @param \Http\Client\HttpClient|NULL      $httpClient
     *   The HTTP client.
     * @param \Http\Message\MessageFactory|NULL $messageFactory
     *   The message factory.
     * @param \Http\Message\UriFactory|NULL     $uriFactory
     *   The URI factory.
     */
    public function __construct(
        HttpClient $httpClient = null,
        MessageFactory $messageFactory = null,
        UriFactory $uriFactory = null
    ) {
        $this->httpClient = $httpClient ?? HttpClientDiscovery::find();
        $this->messageFactory = $messageFactory ?? MessageFactoryDiscovery::find();
        $this->uriFactory = $uriFactory ?? UriFactoryDiscovery::find();
    }

    /**
     * {@inheritdoc}
     */
    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;
        $this->httpClientModified = true;
    }

    /**
     * {@inheritdoc}
     */
    public function removePlugin($fqcn)
    {
        foreach ($this->plugins as $idx => $plugin) {
            if ($plugin instanceof $fqcn) {
                unset($this->plugins[$idx]);
                $this->httpClientModified = true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpClient()
    {
        if ($this->httpClientModified) {
            $this->httpClientModified = false;
            $this->httpClient = new Client(
                (new PluginClientFactory())->createClient($this->httpClient, $this->plugins),
                $this->messageFactory
            );
        }

        return $this->httpClient;
    }
}
