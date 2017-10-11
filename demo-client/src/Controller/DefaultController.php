<?php
namespace App\Controller;

use drupol\pcas\PCas;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var \drupol\pcas\PCas $pCas */
        $pCas = $this->container->get('pcas');

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
          'base_dir'   => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
          'properties' => $pCas->getProperties(),
          'user'       => $this->getUser(),
        ] + $this->defaultVars($pCas, $request));
    }

    public function defaultVars(pCas $pCas, Request $request = null)
    {
        if ($user = $pCas->getAuthenticatedUser()) {
            $name = is_null($user->get('cas:firstName')) ? $user->get('cas:user') : $user->get('cas:firstName') . ' ' . ucfirst(strtolower($user->get('cas:lastName')));

            $welcome = sprintf('Welcome back, %s !', $name);
            $link = [
                'href'  => '/logout',
                'text'  => 'Log out',
                'class' => 'btn btn-danger btn-lg btn-block',
            ];
        } else {
            $welcome = "Welcome, guest !";
            $link = [
                'href'  => '/login',
                'text'  => 'Log in',
                'class' => 'btn btn-success btn-lg btn-block',
            ];
        }

        return [
            'welcome'    => $welcome,
            'link'       => $link,
            'version'    => $pCas::VERSION,
            'properties' => $pCas->getProperties(),
            'server'     => $request->server,
            'session'    => $pCas->getSession()->all(),
            'auth'       => $pCas->isAuthenticated(),
            'user'       => $pCas->getAuthenticatedUser(),
        ];
    }
}
