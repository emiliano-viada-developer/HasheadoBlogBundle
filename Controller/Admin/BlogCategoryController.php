<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogCategory as Category;
use Hasheado\BlogBundle\Form\BlogCategoryType as CategoryType;

class BlogCategoryController extends Controller
{
	/**
     * List action
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('HasheadoBlogBundle:BlogCategory')->findAll();

        return $this->render('HasheadoBlogBundle:Admin\BlogCategory:list.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Add action
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $session = $this->get('session');
        $category = new Category();
        $form = $this->createForm(new CategoryType(), $category);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();

                $session->getFlashBag()->add('success', 'Category was saved!.');
                
                return $this->redirect($this->generateUrl('hasheado_blog_admin_category_edit', array('id' => $category->getId())));

            } else {
                $session->getFlashBag()->add('error', 'There were errors with the form.');
            }
        }

        return $this->render('HasheadoBlogBundle:Admin\BlogCategory:add.html.twig', array(
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
        $category = $em->getRepository('HasheadoBlogBundle:BlogCategory')->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category found for id '.$id
            );
        }

        $form = $this->createForm(new CategoryType(), $category);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($category);
                $em->flush();

                $session->getFlashBag()->add('success', 'Category was saved!.');
                
                return $this->redirect($this->generateUrl('hasheado_blog_admin_category_edit', array('id' => $category->getId())));

            } else {
                $session->getFlashBag()->add('error', 'There were errors with the form.');
            }
        }

        return $this->render('HasheadoBlogBundle:Admin\BlogCategory:edit.html.twig', array(
            'form' => $form->createView(),
            'category_id' => $id
        ));
    }

    /**
     * Delete action
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $category = $em->getRepository('HasheadoBlogBundle:BlogCategory')->find($id);
        if ($category) {
            $em->remove($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'The category was deleted successfully.');

        } else {
            throw $this->createNotFoundException('Unable to find category.');
        }

        return $this->redirect($this->generateUrl('hasheado_blog_admin_category_list'));
    }
}