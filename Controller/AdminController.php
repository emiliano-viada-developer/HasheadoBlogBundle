<?php

namespace Hasheado\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Entity\Post;
use Hasheado\BlogBundle\Form\PostType;

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
