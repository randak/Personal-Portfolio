<?php

namespace KristianRandall\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    public function indexAction()
    {
        return $this->render('KristianRandallPortfolioBundle:Project:index.html.twig');
    }
}
