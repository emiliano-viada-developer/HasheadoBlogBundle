<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogComment as Comment;
use Hasheado\BlogBundle\Form\BlogCommentType as CommentType;

class BlogCommentController extends Controller
{
	/**
     * List action
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('HasheadoBlogBundle:BlogComment')->findAll();

        return $this->render('HasheadoBlogBundle:Admin\BlogComment:list.html.twig', array(
            'comments' => $comments,
        ));
    }

    /**
     * Add action
     */
   public function addAction()
    {
        $request = $this->getRequest();
        $session = $this->get('session');
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                $session->getFlashBag()->add('success', 'Comment was saved!.');
                
                return $this->redirect($this->generateUrl('hasheado_blog_admin_comment_edit', array('id' => $comment->getId())));

            } else {
                $session->getFlashBag()->add('error', 'There were errors with the form.');
            }
        }

        return $this->render('HasheadoBlogBundle:Admin\BlogComment:add.html.twig', array(
        	'form' => $form->createView(),
    	));
    }

    /**
     * Edit action
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $session = $this->get('session');
        $comment = $em->getRepository('HasheadoBlogBundle:BlogComment')->find($id);

        if (!$comment) {
            throw $this->createNotFoundException(
                'No comment found for id '.$id
            );
        }

        $form = $this->createForm(new CommentType(), $comment);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($comment);
                $em->flush();

                $session->getFlashBag()->add('success', 'Comment was saved!.');
                
                return $this->redirect($this->generateUrl('hasheado_blog_admin_comment_edit', array('id' => $comment->getId())));

            } else {
                $session->getFlashBag()->add('error', 'There were errors with the form.');
            }
        }

        return $this->render('HasheadoBlogBundle:Admin\BlogComment:edit.html.twig', array(
            'form' => $form->createView(),
            'comment_id' => $id
        ));
    }

    /**
     * Delete action
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $comment = $em->getRepository('HasheadoBlogBundle:BlogComment')->find($id);
        if ($comment) {
            $em->remove($comment);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'The comment was deleted successfully.');

        } else {
            throw $this->createNotFoundException('Unable to find comment.');
        }

        return $this->redirect($this->generateUrl('hasheado_blog_admin_comment_list'));
    }
}