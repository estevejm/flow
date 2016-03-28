<?php

namespace Flow\Bridge\Symfony\FlowBundle\Controller;

use Flow\Network\Network;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FlowFlowBundle:Default:index.html.twig');
    }

    public function getDataAction()
    {
        /** @var Network $network */
        $network = $this->get('flow.network');

        return new JsonResponse([
            'network'    => $this->get('flow.mapper')->map($network),
            'validation' => $this->get('flow.validator')->validate($network),
        ]);
    }
}
