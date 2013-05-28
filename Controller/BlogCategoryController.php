<?php

namespace Hasheado\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogCategoryController extends Controller
{
	/**
	 * withPosts Action
	 * Shows categories with posts
	 * @return
	 */
	public function withPostsAction()
	{
		$em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('HasheadoBlogBundle:BlogCategory')->getWithPosts();

		return $this->render('HasheadoBlogBundle:BlogCategory:withPosts.html.twig', array(
			'categories' => $categories,
		));
	}
}