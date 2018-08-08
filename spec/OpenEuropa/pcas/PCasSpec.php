<?php
namespace spec\OpenEuropa\pcas;

use OpenEuropa\pcas\Cas\Protocol\V2\CasProtocolV2;
use OpenEuropa\pcas\Http\HttpClientFactory;
use OpenEuropa\pcas\PCas;
use OpenEuropa\pcas\Security\Core\User\PCasUserFactory;
use OpenEuropa\pcas\Utils\PCasSerializerFactory;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class PCasSpec extends ObjectBehavior
{
    public function getProperties()
    {
        return [
            'base_url' => 'http://cas-server',
            'protocol' => [
                'login' => [
                    'path' => '/login',
                    'allowed_parameters' => [
                        'service',
                    ],
                ],
                'logout' => [
                    'path' => '/logout',
                    'allowed_parameters' => [
                        'service',
                    ],
                ],
            ]
        ];
    }

    public function setUpClient()
    {
        $_SERVER['HTTP_HOST'] = 'cas-client';
        $_SERVER['REQUEST_URI'] = '/';
    }

    public function let()
    {
        $this->setUpClient();

        $client = (new HttpClientFactory())->getHttpClient();

        $protocol = new CasProtocolV2(
            new PCasUserFactory(),
            new PCasSerializerFactory());

        $session = new Session();

        $this->beConstructedWith(
            $this->getProperties(),
            $client,
            $protocol,
            $session
        );
    }

    /**
     * @name I can initialize it.
     */
    public function it_is_initializable()
    {
        $this->shouldHaveType(PCas::class);
    }

    /**
     * @name I can login.
     */
    public function it_can_login()
    {
        $properties = $this->getProperties();
        $uri = $properties['base_url'] . $properties['protocol']['login']['path'];
        $url = sprintf('%s?service=%s', $uri, urlencode(sprintf('http://%s/', $_SERVER['HTTP_HOST'])));

        $this->loginUrl()->__toString()->shouldBe($url);
        $this->login()->shouldBeAnInstanceOf(ResponseInterface::class);
        $this->login()->getStatusCode()->shouldBe(302);
    }

    /**
     * @name I can logout.
     */
    public function it_can_logout()
    {
        $properties = $this->getProperties();
        $uri = $properties['base_url'] . $properties['protocol']['logout']['path'];
        $url = sprintf('%s?service=%s', $uri, urlencode(sprintf('http://%s/', $_SERVER['HTTP_HOST'])));

        $this->logoutUrl()->__toString()->shouldBe($url);
        $this->logout()->shouldBeNull();
    }
}
