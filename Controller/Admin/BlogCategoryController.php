<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogCategory as Category;
use Hasheado\BlogBundle\Form\BlogCategoryType as CategoryType;
use Hasheado\BlogBundle\Form\Filter\BlogCategoryFilter;
use Hasheado\BlogBundle\Util\Paginator;

class BlogCategoryController extends Controller
{
	/**
     * List action
     */
    public function listAction($page = 1)
    {
        //Sorting
        list($orderBy, $sortMode, $sortModeReverse) = $this->sorting();
        //Filtering
        list($filterForm, $filtered) = $this->filtering();

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('HasheadoBlogBundle:BlogCategory')->getListQuery($filtered, array($orderBy => $sortMode));

        $paginator = Paginator::getInfo(
            $query,                                                  //Doctrine query
            $this->container->getParameter('admin_items_per_list'),  //Items per list
            $page,                                                   //Page number
            'hasheado_blog_admin_category_pagination'                //Route to paginate
        );

        $categories = $paginator['doctrine_paginator']->getQuery()
                    ->setFirstResult($paginator['offset'])
                    ->setMaxResults($paginator['per_page'])
                    ->getResult();

        return $this->render('HasheadoBlogBundle:Admin\BlogCategory:list.html.twig', array(
            'categories' => $categories,
            'paginator' => $paginator,
            'filter_form' => $filterForm->createView(),
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
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        //Check Reset
        if (stristr($request->getQueryString(), '_reset')) {
            //Reset filters
            if ($session->has('filter')) {
                $filter = json_decode($session->get('filter'), true);
                if (isset($filter['category']))
                    unset($filter['category']);

                $session->set('filter', json_encode($filter));
            }
            //Reset sorting
            if ($session->has('sort')) {
                $sort = json_decode($session->get('sort'), true);
                if (isset($sort['category']))
                    unset($sort['category']);

                $session->set('sort', json_encode($sort));
            }
            
            return $this->redirect($this->generateUrl('hasheado_blog_admin_category_list'));
        }

        if ($request->isMethod('POST')) {
            $filterForm = $this->createForm(new BlogCategoryFilter(array()));
            $filterForm->bind($request);

            $filtered = array();
            if ($filterForm->isValid()) {
                
                $data = $filterForm->getData();
                if (count($data)) {
                    foreach ($data as $key => $value) {
                        if (!is_null($value) && $value != '') {
                            $filtered[$key] = $value;
                        } elseif (is_integer($value) && $value == 0) {
                            $filtered[$key] = $value;
                        } elseif (isset($filtered[$key])) {
                            unset($filtered[$key]);
                        }
                    }
                }
            }

            if ($session->has('filter')) {
                $filter = json_decode($session->get('filter'), true);
                if (isset($filter['category']))
                    unset($filter['category']);
                    
                if (count($filtered))
                    $filter['category'] = $filtered;

                $session->set('filter', json_encode($filter));
            } elseif (count($filtered)) {
                $session->set('filter', json_encode(array('category' => $filtered)));
            }
        }

        if ($session->has('sort'))
            $sort = json_decode($session->get('sort'), true);

        $sort['category'] = array(
                'field' => $field,
                'mode' => $mode
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

    /**
     * filtering method
     */
    protected function filtering()
    {
        $session = $this->get('session');
        $filtered = array();

        if ($session->has('filter')) {
            $filter = json_decode($session->get('filter'), true);
            if(isset($filter['category']))
                $filtered = $filter['category'];
        }

        $filterForm = $this->createForm(new BlogCategoryFilter($filtered));

        return array($filterForm, $filtered);
    }
}