<?php

namespace Hasheado\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogPostController extends Controller
{
	/**
	 * archive Action
	 * Shows posts by month (archive)
	 * @return
	 */
	public function archiveAction()
	{
		$em = $this->getDoctrine()->getManager();
        $months = $em->getRepository('HasheadoBlogBundle:BlogPost')->getArchive();

		return $this->render('HasheadoBlogBundle:BlogPost:archive.html.twig', array(
			'months' => $months,
		));
	}

	/**
	 * popular Action
	 * Shows popular posts
	 * @return
	 */
	public function popularAction()
	{
		$em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('HasheadoBlogBundle:BlogPost')->getPopular($this->container->getParameter('popular_post_sidebar'));

		return $this->render('HasheadoBlogBundle:BlogPost:popular.html.twig', array(
			'posts' => $posts,
		));
	}
}