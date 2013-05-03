<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogCategory as Category;
use Hasheado\BlogBundle\Form\BlogCategoryType as CategoryType;
use Hasheado\BlogBundle\Util\Paginator;

class BlogCategoryController extends Controller
{
	/**
     * List action
     */
    public function listAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('HasheadoBlogBundle:BlogCategory')->findAll();

        //Sorting
        list($orderBy, $sortMode, $sortModeReverse) = $this->sorting();

        $paginator = Paginator::getInfo(
            count($entities),                                        //Total of records
            $this->container->getParameter('admin_items_per_list'),  //Items per list
            $page,                                                   //Page number
            'hasheado_blog_admin_category_pagination'                //Route to paginate
        );

        $categories = $em->getRepository('HasheadoBlogBundle:BlogCategory')->findBy(
            array(), //Criteria (Filtering)
            array($orderBy => $sortMode), //OrderBy (Sortering)
            $paginator['per_page'],
            $paginator['offset']
        );

        return $this->render('HasheadoBlogBundle:Admin\BlogCategory:list.html.twig', array(
            'categories' => $categories,
            'paginator' => $paginator,
            'orderBy' => $orderBy,
            'sort_mode_reverse' => $sortModeReverse,
        ));
    }

    /**
     * Filter action
     */
    public function filterAction($field, $mode)
    {
        $session = $this->get('session');

        $sort = array(
            'category' => array(
                'field' => $field,
                'mode' => $mode
            )
        );

        $session->set('sort', json_encode($sort));

        return $this->redirect($this->generateUrl('hasheado_blog_admin_category_list'));
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

    /**
     * sorting method
     */
    protected function sorting()
    {
        $session = $this->get('session');

        $orderBy = "id";
        $sortMode = "ASC";
        $sortModeReverse = "DESC";
        if ($session->has('sort')) {
            $sort = json_decode($session->get('sort'), true);
            $orderBy = (isset($sort['category']))? $sort['category']['field'] : $orderBy;
            $sortMode = (isset($sort['category']))? $sort['category']['mode'] : $sortMode;
            $sortModeReverse = ($sortMode == "ASC")? "DESC" : "ASC";
        }

        return array($orderBy, $sortMode, $sortModeReverse);
    }
}