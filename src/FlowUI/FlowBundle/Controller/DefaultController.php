<?php

namespace FlowUI\FlowBundle\Controller;

use FlowUI\Component\Validator\Violation;
use FlowUI\Model\Network;
use FlowUI\Model\Node;
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
        $nodes = $this->get('flow.network.builder')->build();
        /** @var Violation[] $violations */
        $violations = $this->get('flow.validator')->validate($nodes);

        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = [
                'nodeId' => $violation->getNode()->getId(),
                'message' => $violation->getMessage(),
                'severity' => $violation->getSeverity(),
            ];
        }

        return new JsonResponse([
            'network' => $this->get('flow.serializer')->serialize($nodes),
            'validator' => [
                'status' => count($violations) == 0 ? 'valid' : 'invalid',
                'errors' => $errors
            ]
        ]);
    }
}
