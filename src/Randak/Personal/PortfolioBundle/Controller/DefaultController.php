<?php

namespace Randak\Personal\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * DefaultController manages parts of the application that are not related to one
 * particular type of object, such as the homepage.
 *
 * @category   Personal
 * @package    Randak\Personal\PortfolioBundle
 * @subpackage Controller
 *
 * @author     Kristian Randall <kristian.l.randall@gmail.com>
 * @license    GNU General Public License, Version 3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.kristianrandall.com/
 */
class DefaultController extends Controller
{
    /**
     * The indexAction returns the contents of the homepage in HTML.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @since 1.0
     */
    public function indexAction()
    {
        return $this->render('RandakPortfolioBundle:Default:index.html.twig');
    }
}
