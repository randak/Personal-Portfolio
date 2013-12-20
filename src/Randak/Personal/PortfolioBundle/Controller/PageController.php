<?php

namespace Randak\Personal\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PageController
 * PageController manages application functionality related to pages.
 *
 * @category   Personal
 * @package    Randak\Personal\PortfolioBundle
 * @subpackage Controller
 *
 * @author     Kristian Randall <kristian.l.randall@gmail.com>
 * @license    GNU General Public License, Version 3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.kristianrandall.com/
 */
class PageController extends Controller
{
    /**
     * singleAction returns a response of a single page in HTML.
     *
     * @param string $page The slug of a particular page to be displayed.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @since 1.0
     */
    public function singleAction($page)
    {
        return $this->render('RandakPortfolioBundle:Page:'.$page.'.html.twig');
    }
}

