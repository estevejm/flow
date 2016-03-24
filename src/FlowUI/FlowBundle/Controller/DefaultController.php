<?php

namespace FlowUI\FlowBundle\Controller;

use FlowUI\FlowBundle\Model\Network;
use FlowUI\FlowBundle\Model\Node;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FlowUIFlowBundle:Default:index.html.twig');
    }

    public function getDataAction()
    {
        /** @var Node[] $data */
        $data = $this->get('test_service')->build();

        $network = new Network($data);

        return new JsonResponse([
           'nodes' => $network->getNodes(),
           'links' => $network->getLinks(),
        ]);
    }
}
