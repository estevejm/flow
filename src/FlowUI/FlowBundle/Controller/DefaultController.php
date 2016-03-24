<?php

namespace FlowUI\FlowBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        echo '<pre>';
        $data = $this->get('test_service')->build();
        var_dump($data);
        //die;
        return $this->render('FlowUIFlowBundle:Default:index.html.twig');
    }
}
