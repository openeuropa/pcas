<?php
namespace spec\OpenEuropa\pcas;

use OpenEuropa\pcas\PCas;
use OpenEuropa\pcas\PCasFactory;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Session\Session;

class PCasFactorySpec extends ObjectBehavior
{
    public function let()
    {
        $session = new Session();
        $this->beConstructedWith(
            $session
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PCasFactory::class);
    }

    public function it_can_generate_pcas()
    {
        $pcas = $this->getPCas();
        $pcas->shouldBeAnInstanceOf(PCas::class);

        $properties = $this->getPCas()->getProperties();
        $properties['base_url']->shouldBe('http://127.0.0.1:8000');
        $properties['protocol']->shouldBe([
            'login' => [
                'path' => '/login',
                'query' => [],
                'allowed_parameters' => [
                    'service',
                    'renew',
                    'gateway',
                ],
            ],
            'servicevalidate' => [
                'path' => '/serviceValidate',
                'query' => [],
                'allowed_parameters' => [
                    'service',
                    'ticket',
                    'pgtUrl',
                    'renew',
                    'format',
                ],
            ],
            'logout' => [
                'path' => '/logout',
                'query' => [],
                'allowed_parameters' => [
                    'service',
                ],
            ],
        ]);
    }

    public function it_can_use_an_external_session_object()
    {
        $session = new Session();
        $session->setName('My session');
        $this->beConstructedWith($session);

        $this->getSession()->getName()->shouldBe('My session');
    }

    public function it_can_generate_custom_pcas()
    {
        $session = new Session();
        $this->beConstructedWith($session, 'http://localhost/cas');
        $pcas = $this->getPCas();
        $properties = $pcas->getProperties();
        $properties['base_url']->shouldBe('http://localhost/cas');
        $properties['protocol']->shouldBe([
            'login' => [
                'path' => '/login',
                'query' => [],
                'allowed_parameters' => [
                    'service',
                    'renew',
                    'gateway',
                ],
            ],
            'servicevalidate' => [
                'path' => '/serviceValidate',
                'query' => [],
                'allowed_parameters' => [
                    'service',
                    'ticket',
                    'pgtUrl',
                    'renew',
                    'format',
                ],
            ],
            'logout' => [
                'path' => '/logout',
                'query' => [],
                'allowed_parameters' => [
                    'service',
                ],
            ],
        ]);

        $uri = $pcas->loginUrl(['service' => 'http://localhost']);
        $uri->getPath()->shouldBe('/cas/login');
    }
}
