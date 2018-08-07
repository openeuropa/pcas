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
    }

    /**
     * @name I can generate a customized pcas object.
     *
     * @throws \Exception
     */
    public function it_can_generate_custom_pcas()
    {
        $parameters = PCasFactory::getDefaultParameters();
        $parameters['logger_startup_message'] = 'Custom message';
        $session = new Session();
        $this->beConstructedWith(
            $session,
            $parameters
        );
        $pcas = $this->getPCas();
        $properties = $pcas->getProperties();
        $properties['logger_startup_message']->shouldBe('Custom message');
    }

    /**
     * @name I override the Pcas parameters.
     *
     * @throws \Exception
     */
    public function it_can_override_parameters()
    {
        $parameters = PCasFactory::getDefaultParameters();
        $parameters['logger_startup_message'] = 'Custom message';
        $this->setParameters($parameters);
        $pcas = $this->getPCas();
        $properties = $pcas->getProperties();
        $properties['logger_startup_message']->shouldBe('Custom message');
    }
}
