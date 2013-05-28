<?php

namespace Hasheado\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogCommentController extends Controller
{
	/**
	 * latest Action
	 * Shows latest comments
	 * @return
	 */
	public function latestAction()
	{
		$em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('HasheadoBlogBundle:BlogComment')->getLatest($this->container->getParameter('comments_sidebar'));

		return $this->render('HasheadoBlogBundle:BlogComment:latest.html.twig', array(
			'comments' => $comments,
		));
	}
}