<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogPost as Post;
use Hasheado\BlogBundle\Form\BlogPostType as PostType;
use Hasheado\BlogBundle\Form\Filter\BlogPostFilter;
use Hasheado\BlogBundle\Util\Paginator;

class BlogPostController extends Controller
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
        $query = $em->getRepository('HasheadoBlogBundle:BlogPost')->getListQuery($filtered, array($orderBy => $sortMode));

        $paginator = Paginator::getInfo(
            $query,                                                  //Doctrine query
            $this->container->getParameter('admin_items_per_list'),  //Items per list
            $page,                                                   //Page number
            'hasheado_blog_admin_post_pagination'                    //Route to paginate
        );

        $posts = $paginator['doctrine_paginator']->getQuery()
                    ->setFirstResult($paginator['offset'])
                    ->setMaxResults($paginator['per_page'])
                    ->getResult();

        return $this->render('HasheadoBlogBundle:Admin\BlogPost:list.html.twig', array(
            'posts' => $posts,
            'paginator' => $paginator,
            'filter_form' => $filterForm->createView(),
            'orderBy' => $orderBy,
            'sort_mode_reverse' => $sortModeReverse,
            'filtered' => (count($filtered))? true : false,
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
                if (isset($filter['post']))
                    unset($filter['post']);

                $session->set('filter', json_encode($filter));
            }
            //Reset sorting
            if ($session->has('sort')) {
                $sort = json_decode($session->get('sort'), true);
                if (isset($sort['post']))
                    unset($sort['post']);

                $session->set('sort', json_encode($sort));
            }

            return $this->redirect($this->generateUrl('hasheado_blog_admin_post_list'));
        }

        if ($request->isMethod('POST')) {
            $filterForm = $this->createForm(new BlogPostFilter(array(), $em));
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
                if (isset($filter['post']))
                    unset($filter['post']);
                
                if (count($filtered))
                    $filter['post'] = $filtered;
                
                $session->set('filter', json_encode($filter));
            } elseif (count($filtered)) {
                $session->set('filter', json_encode(array('post' => $filtered)));
            }
        }

        if ($session->has('sort'))
            $sort = json_decode($session->get('sort'), true);

        $sort['post'] = array(
                'field' => $field,
                'mode' => $mode
        );

        $session->set('sort', json_encode($sort));

        return $this->redirect($this->generateUrl('hasheado_blog_admin_post_list'));
    }

    /**
     * Add action
     */
    public function addAction()
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

        return $this->render('HasheadoBlogBundle:Admin\BlogPost:add.html.twig', array(
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

        return $this->render('HasheadoBlogBundle:Admin\BlogPost:edit.html.twig', array(
            'form' => $form->createView(),
            'post_id' => $id
        ));
    }

    /**
     * Delete action
     */
    public function deleteAction($id)
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
            $orderBy = (isset($sort['post']))? $sort['post']['field'] : $orderBy;
            $sortMode = (isset($sort['post']))? $sort['post']['mode'] : $sortMode;
            $sortModeReverse = ($sortMode == "ASC")? "DESC" : "ASC";
        }

        return array($orderBy, $sortMode, $sortModeReverse);
    }

    /**
     * filtering method
     */
    protected function filtering()
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $filtered = array();

        if ($session->has('filter')) {
            $filter = json_decode($session->get('filter'), true);
            if(isset($filter['post']))
                $filtered = $filter['post'];
        }

        $filterForm = $this->createForm(new BlogPostFilter($filtered, $em));

        return array($filterForm, $filtered);
    }
}
