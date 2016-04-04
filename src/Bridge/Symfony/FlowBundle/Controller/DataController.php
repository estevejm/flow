<?php

namespace EJM\Flow\Bridge\Symfony\FlowBundle\Controller;

use EJM\Flow\Network\Network;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function graphAction()
    {
        /** @var Network $network */
        $network = $this->get('flow.network');

        return new JsonResponse($this->get('flow.mapper')->map($network));
    }

    /**
     * @return JsonResponse
     */
    public function validationAction()
    {
        /** @var Network $network */
        $network = $this->get('flow.network');

        return new JsonResponse($this->get('flow.validator')->validate($network));
    }
}
