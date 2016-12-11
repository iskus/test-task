<?php

namespace Foo\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FooBundle:Default:index.html.twig');
    }
}
