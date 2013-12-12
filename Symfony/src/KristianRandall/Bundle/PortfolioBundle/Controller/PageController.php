<?php

namespace KristianRandall\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('KristianRandallPortfolioBundle:Page:index.html.twig');
    }
}
