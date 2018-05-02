<?php
namespace App\Controller;

use function GuzzleHttp\Psr7\parse_query;
use GuzzleHttp\Psr7\Uri;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function indexAction(Request $request)
    {
        /** @var \Psr\SimpleCache\CacheInterface $cache */
        $cache = $this->container->get('app.cache');
        $users = $cache->get('users', []);

        $user = null;
        if ($request->hasSession()) {
            $user = $request->getSession()->get('user');
        }

        if ('true' === $request->get('renew', 'false') || is_null($user) || !isset($users[$user])) {
            $form = $this->createFormBuilder()
                ->add('user', TextType::class)
                ->add('nickname', TextType::class)
                ->add('password', PasswordType::class)
                ->add('email', HiddenType::class, array(
                    'data' => $this->generateRandomEmail(),
                ))
                ->add('submit', SubmitType::class, array('label' => 'Submit'))
                ->getForm();

        } else {
            $form = $this->createFormBuilder()
                ->add('password', PasswordType::class)
                ->add('submit', SubmitType::class, array('label' => 'Submit'))
                ->getForm();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uri = new Uri($request->get('service'));
            $query = parse_query($uri->getQuery());
            $query['ticket'] = 'ST-' . sha1(uniqid());
            $query = array_merge($query, $form->getData());
            $user = $request->getSession()->get('user');

            if ('true' === $request->get('renew', 'false') || is_null($user) || !isset($users[$user])) {
                $users[$query['ticket']] = [
                    'data' => $form->getData(),
                    'auth' => 0,
                    'ticket' => $query['ticket'],
                ];
            } else {
                if ($request->getSession()->has('user')) {
                    $users[$query['ticket']] = [
                        'data' => array_merge($users[$user]['data'], $form->getData()),
                        'auth' => 0,
                        'ticket' => $query['ticket'],
                    ];
                }
            }

            $cache->set('users', $users);

            $response = $this->redirect($uri->withQuery(http_build_query($query))->__toString());
            $request->getSession()->set('user', $query['ticket']);

            return $response;
        }

        return $this->render('default/login.html.twig', array(
          'form' => $form->createView(),
        ));
    }

    /**
     * Generates random email.
     *
     * @source http://www.jonhaworth.com/articles/php/generate-random-email-addresses
     *
     * @return string
     */
    private function generateRandomEmail()
    {
        $return = '';

        // array of possible top-level domains
        $tlds = array('com', 'net', 'gov', 'org', 'edu', 'biz', 'info', 'be');

        // string of possible characters
        $char = '0123456789abcdefghijklmnopqrstuvwxyz';

        // choose random lengths for the username ($ulen) and the domain ($dlen)
        $ulen = mt_rand(5, 10);
        $dlen = mt_rand(7, 17);

        // get $ulen random entries from the list of possible characters
        // these make up the username (to the left of the @)
        for ($i = 1; $i <= $ulen; $i++) {
            $return .= substr($char, mt_rand(0, strlen($char)), 1);
        }

        // wouldn't work so well without this
        $return .= "@";

        // now get $dlen entries from the list of possible characters
        // this is the domain name (to the right of the @, excluding the tld)
        for ($i = 1; $i <= $dlen; $i++) {
            $return .= substr($char, mt_rand(0, strlen($char)), 1);
        }

        // need a dot to separate the domain from the tld
        $return .= ".";

        // finally, pick a random top-level domain and stick it on the end
        $return .= $tlds[mt_rand(0, (sizeof($tlds)-1))];

        return $return;
    }

}
