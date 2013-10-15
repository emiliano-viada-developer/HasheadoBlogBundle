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

	/** Posts by category */
    public function byCategoryAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        if ($slug != 'uncategorized') {
            $category = $em->getRepository('HasheadoBlogBundle:BlogCategory')->findOneBySlug($slug);

            if (!$category) {
                throw $this->createNotFoundException(
                    'No category found for slug '.$slug
                );
            }
            $posts = $category->getPosts();

        } else {
            $category = new Category(); //temporal
            $category->setName('Uncategorized');
            //Get uncategorized posts
            $posts = $em->getRepository('HasheadoBlogBundle:BlogPost')->getUncategorized();
        }

        return $this->render('HasheadoBlogBundle:BlogCategory:by_category.html.twig', array(
            'category' => $category,
            'posts' => $posts,
        ));
    }
}