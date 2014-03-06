<?php

namespace Hasheado\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogComment as Comment;
use Hasheado\BlogBundle\Form\BlogCommentPostType as CommentType;
use Hasheado\BlogBundle\Util\Util;

class BlogPostController extends Controller
{
	/** 
	 * Post detail action
	 * @param  string $slug
	 * @return
	 */
    public function postDetailAction($slug)
    {
    	//Redirect if slug = admin
    	if ($slug === 'admin')
    		return $this->redirect($this->generateUrl('hasheado_blog_admin_dashboard'));

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
			'monthArray' => Util::monthNameToNumber()
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

	/**
	 * tags Action
	 * Shows Tags
	 * @return
	 */
	public function tagsAction()
	{
		$em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('HasheadoBlogBundle:BlogTag')->getPopular($this->container->getParameter('tags_sidebar'));

		return $this->render('HasheadoBlogBundle:BlogPost:tags.html.twig', array(
			'tags' => $tags,
		));
	}

	/**
	 * byTag Action
	 * Shows posts by tag
	 * @return
	 */
	public function byTagAction($slug)
	{
		$em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository('HasheadoBlogBundle:BlogTag')->findOneBySlug($slug);

        if (!$tag) {
            throw $this->createNotFoundException(
                'No tag found for slug '.$slug
            );
        }

        $posts = $tag->getPosts();

        if (count($posts)) {
        	foreach ($posts as $i => $post) {
        		if (!$post->getIsPublished()) {
        			unset($posts[$i]);
        		}
        	}
        }

		return $this->render('HasheadoBlogBundle:BlogPost:by_tag.html.twig', array(
			'tag' => $tag,
			'posts' => $posts,
		));
	}
}