<?php

namespace Hasheado\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogPost as Post;
use Hasheado\BlogBundle\Form\BlogPostType as PostType;

class AdminController extends Controller
{
    /**
     * PostList action
     */
    public function postListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('HasheadoBlogBundle:BlogPost')->findAll();

        return $this->render('HasheadoBlogBundle:Admin:post_list.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * AddPost action
     */
    public function addPostAction()
    {
        $request = $this->getRequest();
        $session = $this->get('session');
        $post = new Post();
        $form = $this->createForm(new PostType(), $post);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

                $session->getFlashBag()->add('success', 'Post was saved!.');
                
                return $this->redirect($this->generateUrl('hasheado_blog_admin_post_edit', array('id' => $post->getId())));

            } else {
                $session->getFlashBag()->add('error', 'There were errors with the form.');
            }
        }

        return $this->render('HasheadoBlogBundle:Admin:add_post.html.twig', array(
        	'form' => $form->createView(),
    	));
    }

    /**
     * EditPost action
     */
    public function editPostAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $session = $this->get('session');
        $post = $em->getRepository('HasheadoBlogBundle:BlogPost')->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }

        $form = $this->createForm(new PostType(), $post);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($post);
                $em->flush();

                $session->getFlashBag()->add('success', 'Post was saved!.');
                
                return $this->redirect($this->generateUrl('hasheado_blog_admin_post_edit', array('id' => $post->getId())));

            } else {
                $session->getFlashBag()->add('error', 'There were errors with the form.');
            }
        }

        return $this->render('HasheadoBlogBundle:Admin:edit_post.html.twig', array(
            'form' => $form->createView(),
            'post_id' => $id
        ));
    }

    /**
     * DeletePost action
     */
    public function deletePostAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $post = $em->getRepository('HasheadoBlogBundle:BlogPost')->find($id);
        if ($post) {
            $em->remove($post);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'The post was deleted successfully.');

        } else {
            throw $this->createNotFoundException('Unable to find post.');
        }

        return $this->redirect($this->generateUrl('hasheado_blog_admin_post_list'));
    }
}
