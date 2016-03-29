<?php

namespace EJM\Flow\Bridge\Symfony\FlowBundle\Controller;

use EJM\Flow\Network\Network;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('FlowFlowBundle:Default:index.html.twig');
    }

    /**
     * @return JsonResponse
     */
    public function dataAction()
    {
        /** @var Network $network */
        $network = $this->get('flow.network');

        return new JsonResponse([
            'network'    => $this->get('flow.mapper')->map($network),
            'validation' => $this->get('flow.validator')->validate($network),
        ]);
    }
}
