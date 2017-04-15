<?php

namespace CEM\Ui\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET", "HEAD"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $url = sprintf(
            '%s://%s',
            $this->getParameter('router.request_context.scheme'),
            $this->getParameter('host.dashboard')
        );

        $response = new RedirectResponse($url, 301);

        if ($request->get('origin')) {
            $cookie = new Cookie(
                'origin',
                $request->get('origin'),
                0,
                '/',
                $this->getParameter('cookie_domain'),
                false,
                false
            );
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
