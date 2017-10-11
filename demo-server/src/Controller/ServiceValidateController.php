<?php
namespace App\Controller;

use function GuzzleHttp\Psr7\parse_query;
use GuzzleHttp\Psr7\Uri;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ServiceValidateController extends Controller
{
    /**
     * @Route("/serviceValidate", name="service_validate", defaults={"_format"="xml"})
     */
    public function indexAction(Request $request)
    {
        /** @var \Psr\SimpleCache\CacheInterface $cache */
        $cache = $this->container->get('app.cache');
        $users = $cache->get('users', []);
        $parameters = [];

        if ($user = $request->get('ticket')) {
            if (isset($users[$user])) {
                // Do not allow multiple use of a ticket.
                if (0 == $users[$user]['auth']) {
                    $parameters = $users[$user]['data'];
                    $users[$user]['auth']++;
                    $cache->set('users', $users);
                }
            }
        }

        if ($parameters) {
            return $this->render('default/service_validate_success.html.twig', array(
                'parameters' => $parameters,
            ));
        } else {
            return $this->render('default/service_validate_failure.html.twig');
        }
    }
}
