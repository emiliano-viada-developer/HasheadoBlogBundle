<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /** Dashboard */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = count($em->getRepository('HasheadoBlogBundle:BlogCategory')->findAll());
        $posts = count($em->getRepository('HasheadoBlogBundle:BlogPost')->findAll());
        $comments = count($em->getRepository('HasheadoBlogBundle:BlogComment')->findAll());

        return $this->render('HasheadoBlogBundle:Admin\Default:dashboard.html.twig', array(
        	'categories' => $categories,
        	'posts' => $posts,
        	'comments' => $comments,
    	));
    }
}
