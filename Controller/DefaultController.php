<?php

namespace BiberLtd\Bundle\ContactInformationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BiberLtdContactInformationBundle:Default:index.html.twig', array('name' => $name));
    }
}
