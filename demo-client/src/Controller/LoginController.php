<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends DefaultController
{
    /**
     * @Route("/login", name="login")
     */
    public function indexAction(Request $request)
    {
        /** @var \OpenEuropa\pcas\PCas $pCas */
        $pCas = $this->container->get('pcas');

        $redirect = $this->redirectToRoute('homepage');

        if ($response = $pCas->login()) {
            $redirect = (new HttpFoundationFactory())->createResponse($response);
        }

        return $redirect;
    }
}
