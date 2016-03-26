<?php

namespace FlowUI\FlowBundle\Controller;

use FlowUI\Component\Validator\Violation;
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
        /** @var Node[] $nodes */
        $nodes = $this->get('test_service')->build();
        /** @var Violation[] $violations */
        $violations = $this->get('flow.validator')->validate($nodes);

        $network = new Network($nodes);

        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = [
                'nodeId' => $violation->getNode()->getId(),
                'message' => $violation->getMessage(),
                'severity' => $violation->getSeverity(),
            ];
        }

        return new JsonResponse([
            'network' => [
                'nodes' => $network->getNodes(),
                'links' => $network->getLinks(),
            ],
            'validator' => [
                'status' => count($violations) == 0 ? 'valid' : 'invalid',
                'errors' => $errors
            ]
        ]);
    }
}
