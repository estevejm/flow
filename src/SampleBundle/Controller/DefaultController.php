<?php

namespace SampleBundle\Controller;

use SampleBundle\Command\ExecuteCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $this->get('command_bus')->handle(new ExecuteCommand('yuhu!'));

        return $this->render('SampleBundle:Default:index.html.twig');
    }
}
