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

    /**
     * @name I can initialize it.
     */
    public function it_is_initializable()
    {
        $this->shouldHaveType(PCasFactory::class);
    }

    /**
     * @name I can generate a pcas object.
     *
     * @throws \Exception
     */
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

    /**
     * @name I can use an external session object.
     *
     * @throws \Exception
     */
    public function it_can_use_an_external_session_object()
    {
        $session = new Session();
        $session->setName('My session');
        $this->beConstructedWith($session);

        $this->getSession()->getName()->shouldBe('My session');
    }

    /**
     * @name I can generate a customized pcas object.
     *
     * @throws \Exception
     */
    public function it_can_generate_custom_pcas()
    {
        $session = new Session();
        $this->beConstructedWith($session, ['base_url' => 'http://localhost']);
        $properties = $this->getPCas()->getProperties();
        $properties['base_url']->shouldBe('http://localhost');
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
}
