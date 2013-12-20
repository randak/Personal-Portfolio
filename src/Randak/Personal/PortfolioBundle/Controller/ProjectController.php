<?php

namespace Randak\Personal\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ProjectController
 * ProjectController manages parts of the application related to the display or
 * processing of projects.
 *
 * @category   Personal
 * @package    Randak\Personal\PortfolioBundle
 * @subpackage Controller
 *
 * @author     Kristian Randall <kristian.l.randall@gmail.com>
 * @license    GNU General Public License, Version 3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.kristianrandall.com/
 */
class ProjectController extends Controller
{
    /**
     * singleAction returns a response of a single project in HTML.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @since 1.0
     */
    public function singleAction()
    {
        return $this->render('RandakPortfolioBundle:Project:single.html.twig');
    }
}
