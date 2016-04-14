<?php

namespace EJM\Flow\Bridge\Symfony\FlowUIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('EJMFlowUIBundle:Default:index.html.twig', [
            'graph_url' => $this->generateUrl('ejm_flow_data_graph'),
            'validation_url' => $this->generateUrl('ejm_flow_data_validation'),
        ]);
    }
}
