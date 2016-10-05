<?php

namespace Petkopara\MultiSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PetkoparaMultiSearchBundle:Default:index.html.twig');
    }
}
