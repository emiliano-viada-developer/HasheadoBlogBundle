<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogComment as Comment;
use Hasheado\BlogBundle\Form\BlogCommentType as CommentType;
use Hasheado\BlogBundle\Util\Paginator;

class BlogCommentController extends Controller
{
	/**
     * List action
     */
    public function listAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('HasheadoBlogBundle:BlogComment')->findAll();

        //Sorting
        list($orderBy, $sortMode, $sortModeReverse) = $this->sorting();

        $paginator = Paginator::getInfo(
            count($entities),                                        //Total of records
            $this->container->getParameter('admin_items_per_list'),  //Items per list
            $page,                                                   //Page number
            'hasheado_blog_admin_comment_pagination'                 //Route to paginate
        );

        $comments = $em->getRepository('HasheadoBlogBundle:BlogComment')->findBy(
            array(), //Criteria (Filtering)
            array($orderBy => $sortMode), //OrderBy (Sortering)
            $paginator['per_page'],
            $paginator['offset']
        );

        return $this->render('HasheadoBlogBundle:Admin\BlogComment:list.html.twig', array(
            'comments' => $comments,
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
            'comment' => array(
                'field' => $field,
                'mode' => $mode
            )
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
}