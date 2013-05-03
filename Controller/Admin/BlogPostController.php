<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogPost as Post;
use Hasheado\BlogBundle\Form\BlogPostType as PostType;
use Hasheado\BlogBundle\Util\Paginator;

class BlogPostController extends Controller
{
    /**
     * List action
     */
    public function listAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('HasheadoBlogBundle:BlogPost')->findAll();

        //Sorting
        list($orderBy, $sortMode, $sortModeReverse) = $this->sorting();

        $paginator = Paginator::getInfo(
            count($entities),                                        //Total of records
            $this->container->getParameter('admin_items_per_list'),  //Items per list
            $page,                                                   //Page number
            'hasheado_blog_admin_post_pagination'                    //Route to paginate
        );

        $posts = $em->getRepository('HasheadoBlogBundle:BlogPost')->findBy(
            array(), //Criteria (Filtering)
            array($orderBy => $sortMode), //OrderBy (Sortering)
            $paginator['per_page'],
            $paginator['offset']
        );

        return $this->render('HasheadoBlogBundle:Admin\BlogPost:list.html.twig', array(
            'posts' => $posts,
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
            'post' => array(
                'field' => $field,
                'mode' => $mode
            )
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
}
