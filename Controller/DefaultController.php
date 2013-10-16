<?php

namespace Hasheado\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Hasheado\BlogBundle\Entity\BlogComment as Comment;
use Hasheado\BlogBundle\Form\BlogCommentPostType as CommentType;
use Hasheado\BlogBundle\Util\Paginator;
use Hasheado\BlogBundle\Util\Util;

class DefaultController extends Controller
{
    /** Homepage */
    public function indexAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('HasheadoBlogBundle:BlogPost')->getListQuery(
            array('isPublished' => 1),
            array('publishedAt' => 'DESC')
        );

        $paginator = Paginator::getInfo(
            $query,                                                  //Doctrine query
            $this->container->getParameter('post_in_homepage'),      //Items per list
            $page,                                                   //Page number
            'hasheado_blog_homepage_pagination'                      //Route to paginate
        );

        $posts = $paginator['doctrine_paginator']->getQuery()
                    ->setFirstResult($paginator['offset'])
                    ->setMaxResults($paginator['per_page'])
                    ->getResult();

        return $this->render('HasheadoBlogBundle:Default:index.html.twig', array(
        	'posts' => $posts,
            'paginator' => $paginator,
    	));
    }

    /** Comment action */
    public function commentAction()
    {
        $request = $this->getRequest();

        if ($request->isMethod('POST')) {

            $comment = new Comment();
            $form = $this->createForm(new CommentType(), $comment);

            $form->bind($request);
            $post = $comment->getPost();
            if ($form->isValid()) {
                $comment->setIsAccepted(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                
                return $this->redirect(
                    $this->generateUrl('hasheado_blog_post_detail', array('slug' => $post->getSlug())) . '#comment-' . $comment->getId()
                );

            } else { //If it's not valid, render comment form with errors.
                return $this->render('HasheadoBlogBundle:Default:post_detail.html.twig', array(
                    'post' => $post,
                    'form' => $form->createView(),
                ));
            }

        } else {
            throw $this->createNotFoundException('Page not found.');
        }
    }

    /**
     * archiveAction()
     * @param int $year
     * @param int $month
     * @return
     */
    public function archiveAction($year, $month)
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('HasheadoBlogBundle:BlogPost')->getArchiveByDate($year, $month);

        $monthArray = array_flip(Util::monthNameToNumber());

        return $this->render('HasheadoBlogBundle:Default:archive.html.twig', array(
            'posts' => $posts,
            'year' => $year,
            'month' => $monthArray[$month]
        ));
    }
}
