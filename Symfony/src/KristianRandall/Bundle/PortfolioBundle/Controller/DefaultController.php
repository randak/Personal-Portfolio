<?php

namespace KristianRandall\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KristianRandallPortfolioBundle:Default:index.html.twig');
    }
}
