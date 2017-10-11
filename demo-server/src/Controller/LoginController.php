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

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
          ->add('user', TextType::class)
          ->add('password', PasswordType::class)
          ->add('submit', SubmitType::class, array('label' => 'Submit'))
          ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uri = new Uri($request->get('service'));
            $query = parse_query($uri->getQuery());
            $query['ticket'] = 'ST-' . sha1(json_encode($form->getData()));
            $query = array_merge($query, $form->getData());

            return $this->redirect($uri->withQuery(http_build_query($query))->__toString());
        }

        return $this->render('default/login.html.twig', array(
          'form' => $form->createView(),
        ));
    }
}
