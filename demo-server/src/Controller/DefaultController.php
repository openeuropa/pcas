<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var \Psr\SimpleCache\CacheInterface $cache */
        $cache = $this->container->get('app.cache');

        return $this->render('default/index.html.twig', [
            'users' => $cache->get('users', []),
        ]);
    }
}
