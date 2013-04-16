<?php

namespace Hasheado\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\BlogPost as Post;
use Hasheado\BlogBundle\Form\BlogPostType as PostType;

class AdminController extends Controller
{
    /**
     * AddPost action
     */
    public function addPostAction()
    {
        $post = new Post();
        $form = $this->createForm(new PostType(), $post);

        return $this->render('HasheadoBlogBundle:Admin:add_post.html.twig', array(
        	'form' => $form->createView(),
    	));
    }
}
