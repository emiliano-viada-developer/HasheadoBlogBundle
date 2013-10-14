<?php

namespace Hasheado\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogComment as Comment;
use Hasheado\BlogBundle\Form\BlogCommentPostType as CommentType;

class BlogPostController extends Controller
{
	/** 
	 * Post detail action
	 * @param  string $slug
	 * @return
	 */
    public function postDetailAction($slug)
    {
    	$em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('HasheadoBlogBundle:BlogPost')->findOneBySlug($slug);

    	if (!$post) {
            throw $this->createNotFoundException(
                'No post found for slug '.$slug
            );
        }

        $comment = new Comment();
        $comment->setPost($post);
        $comment_form = $this->createForm(new CommentType(), $comment);

    	return $this->render('HasheadoBlogBundle:BlogPost:post_detail.html.twig', array(
    		'post' => $post,
    		'form' => $comment_form->createView(),
		));
    }

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