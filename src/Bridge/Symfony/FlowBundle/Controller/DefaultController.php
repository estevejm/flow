<?php

namespace EJM\Flow\Bridge\Symfony\FlowBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('EJMFlowBundle:Default:index.html.twig');
    }
}
