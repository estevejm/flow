<?php

namespace FlowUI\FlowBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        echo '<pre>';
        $handler = $this->get('test_service')->build();
        die;
        return $this->render('FlowUIFlowBundle:Default:index.html.twig');
    }
}
