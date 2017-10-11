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
        $service = parse_url($request->get('service'));
        $parameters = parse_query($service['query']);

        return $this->render('default/service_validate.html.twig', array(
            'parameters' => $parameters,
        ));
    }
}
