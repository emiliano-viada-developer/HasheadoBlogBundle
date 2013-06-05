<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /** Dashboard */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = count($em->getRepository('HasheadoBlogBundle:BlogCategory')->findAll());
        $posts = count($em->getRepository('HasheadoBlogBundle:BlogPost')->findAll());
        $comments = count($em->getRepository('HasheadoBlogBundle:BlogComment')->findAll());

        return $this->render('HasheadoBlogBundle:Admin\Default:dashboard.html.twig', array(
        	'categories' => $categories,
        	'posts' => $posts,
        	'comments' => $comments,
    	));
    }

    /** Breadcrumb */
    public function breadcrumbAction($current_route)
    {
        $categoriesRoutes = array('hasheado_blog_admin_category_list', 'hasheado_blog_admin_category_pagination',
                                'hasheado_blog_admin_category_add', 'hasheado_blog_admin_category_edit');
        $postsRoutes = array('hasheado_blog_admin_post_list', 'hasheado_blog_admin_post_pagination',
                                'hasheado_blog_admin_post_add', 'hasheado_blog_admin_post_edit');
        $commentsRoutes = array('hasheado_blog_admin_comment_list', 'hasheado_blog_admin_comment_pagination',
                                'hasheado_blog_admin_comment_add', 'hasheado_blog_admin_comment_edit');
        $tagsRoutes = array('hasheado_blog_admin_tag_list', 'hasheado_blog_admin_tag_pagination',
                                'hasheado_blog_admin_tag_add', 'hasheado_blog_admin_tag_edit');

        $breadcrumb = array(
            0 => array(
                'route' => 'hasheado_blog_admin_dashboard',
                'label' => 'Home'
            )
        );

        if ($current_route == 'hasheado_blog_admin_dashboard') {
            $breadcrumb[1]['label'] = 'Dashboard';
        } elseif (in_array($current_route, $categoriesRoutes)) {
            $breadcrumb[1]['route'] = 'hasheado_blog_admin_category_list';
            $breadcrumb[1]['label'] = 'Categories';
        } elseif (in_array($current_route, $postsRoutes)) {
            $breadcrumb[1]['route'] = 'hasheado_blog_admin_post_list';
            $breadcrumb[1]['label'] = 'Posts';
        } elseif (in_array($current_route, $commentsRoutes)) {
            $breadcrumb[1]['route'] = 'hasheado_blog_admin_comment_list';
            $breadcrumb[1]['label'] = 'Comments';
        } elseif (in_array($current_route, $tagsRoutes)) {
            $breadcrumb[1]['route'] = 'hasheado_blog_admin_tag_list';
            $breadcrumb[1]['label'] = 'Tags';
        }

        if (stristr($current_route, 'add')) {
            $breadcrumb[2]['label'] = 'Add';
        } elseif (stristr($current_route, 'edit')) {
            $breadcrumb[2]['label'] = 'Edit';
        }

        return $this->render('HasheadoBlogBundle:Admin\Default:breadcrumb.html.twig', array(
            'breadcrumb' => $breadcrumb,
        ));
    }
}
