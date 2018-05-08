<?php
namespace OpenEuropa\pcas\Http;

use Http\Client\Common\HttpClientDecorator;
use Http\Client\Common\Plugin\HeaderSetPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Exception\TransferException;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\MessageFactory;
use Http\Message\UriFactory;

/**
 * Class Client.
 */
class Client implements PCasHttpClientInterface
{
    use HttpClientDecorator;

    /**
     * The URI Factory.
     *
     * @var UriFactory
     */
    protected $uriFactory;

    /**
     * The message factory.
     *
     * @var \Http\Message\MessageFactory
     */
    private $messageFactory;

    /**
     * Client constructor.
     *
     * @param \Http\Client\HttpClient|NULL $httpClient
     *   The HTTP client.
     * @param \Http\Message\MessageFactory|NULL $messageFactory
     *   The message factory.
     * @param \Http\Message\UriFactory|NULL $uriFactory
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

        $headerPlugin = new HeaderSetPlugin([
            'User-Agent' => 'openeuropa/pcas library'
        ]);

        $this->httpClient = new PluginClient(
            $this->httpClient,
            [$headerPlugin]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function request($url, $method = 'GET', array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        $request = $this->messageFactory->createRequest($method, $url, $headers, $body, $protocolVersion);

        try {
            return $this->sendRequest($request);
        } catch (TransferException $e) {
            throw new \Exception('Error while requesting data from CAS: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function redirect($url, $replace = true, $code = 302)
    {
        return $this->messageFactory->createResponse($code, '', [
          'Location' => $url,
        ]);
    }
}
