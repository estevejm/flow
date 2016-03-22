<?php

namespace SampleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SampleBundle:Default:index.html.twig');
    }
}
