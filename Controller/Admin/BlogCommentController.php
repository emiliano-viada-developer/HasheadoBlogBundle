<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogComment as Comment;
use Hasheado\BlogBundle\Form\BlogCommentType as CommentType;
use Hasheado\BlogBundle\Form\Filter\BlogCommentFilter;
use Hasheado\BlogBundle\Util\Paginator;

class BlogCommentController extends Controller
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
        $query = $em->getRepository('HasheadoBlogBundle:BlogComment')->getListQuery($filtered, array($orderBy => $sortMode));

        $paginator = Paginator::getInfo(
            $query,                                                  //Doctrine query
            $this->container->getParameter('admin_items_per_list'),  //Items per list
            $page,                                                   //Page number
            'hasheado_blog_admin_comment_pagination'                 //Route to paginate
        );

        $comments = $paginator['doctrine_paginator']->getQuery()
                    ->setFirstResult($paginator['offset'])
                    ->setMaxResults($paginator['per_page'])
                    ->getResult();

        return $this->render('HasheadoBlogBundle:Admin\BlogComment:list.html.twig', array(
            'comments' => $comments,
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
                if (isset($filter['comment']))
                    unset($filter['comment']);

                $session->set('filter', json_encode($filter));
            }
            //Reset sorting
            if ($session->has('sort')) {
                $sort = json_decode($session->get('sort'), true);
                if (isset($sort['comment']))
                    unset($sort['comment']);

                $session->set('sort', json_encode($sort));
            }

            return $this->redirect($this->generateUrl('hasheado_blog_admin_comment_list'));
        }

        if ($request->isMethod('POST')) {
            $filterForm = $this->createForm(new BlogCommentFilter(array(), $em));
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
                if (isset($filter['comment']))
                    unset($filter['comment']);
                
                if (count($filtered))
                    $filter['comment'] = $filtered;
                
                $session->set('filter', json_encode($filter));
            } elseif (count($filtered)) {
                $session->set('filter', json_encode(array('comment' => $filtered)));
            }
        }

        if ($session->has('sort'))
            $sort = json_decode($session->get('sort'), true);

        $sort['comment'] = array(
                'field' => $field,
                'mode' => $mode
        );

        $session->set('sort', json_encode($sort));

        return $this->redirect($this->generateUrl('hasheado_blog_admin_comment_list'));
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
            $orderBy = (isset($sort['comment']))? $sort['comment']['field'] : $orderBy;
            $sortMode = (isset($sort['comment']))? $sort['comment']['mode'] : $sortMode;
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
            if(isset($filter['comment']))
                $filtered = $filter['comment'];
        }

        $filterForm = $this->createForm(new BlogCommentFilter($filtered, $em));

        return array($filterForm, $filtered);
    }
}