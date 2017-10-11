<?php
namespace App\Controller;

use GuzzleHttp\Psr7\Uri;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LogoutController extends Controller
{
    /**
     * @Route("/logout", name="logout")
     */
    public function indexAction(Request $request)
    {
        /** @var \Psr\SimpleCache\CacheInterface $cache */
        $cache = $this->container->get('app.cache');
        $users = $cache->get('users', []);

        if ($request->hasSession() && $user = $request->getSession()->get('user')) {
            unset($users[$user]);
            $cache->set('users', $users);
            $request->getSession()->clear();
        }

        $uri = new Uri($request->get('service'));
        return $this->redirect($uri->__toString());
    }
}
