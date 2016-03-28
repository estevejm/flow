<?php

namespace Flow\Bridge\Symfony\FlowBundle\Controller;

use Flow\Network\Network;
use Flow\Validator\Validation;
use Flow\Network\Node;
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

        /** @var Validation $validation */
        $validation = $this->get('flow.validator')->validate($network);

        $violations = [];

        // todo: create validation serializer
        foreach ($validation->getViolations() as $violation) {
            $violations[] = [
                'nodeId' => $violation->getNode()->getId(),
                'message' => $violation->getMessage(),
                'severity' => $violation->getSeverity(),
            ];
        }

        return new JsonResponse([
            'network' => $this->get('flow.serializer')->serialize($network),
            'validation' => [
                'status' => $validation->getStatus(),
                'violations' => $violations
            ]
        ]);
    }
}
