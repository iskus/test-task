<?php

namespace Chain\CommandBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ChainCommandBundle:Default:index.html.twig');
    }
}
