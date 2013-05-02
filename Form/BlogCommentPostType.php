<?php

namespace Hasheado\BlogBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Hasheado\BlogBundle\Form\BlogCommentType;

class BlogCommentPostType extends BlogCommentType
{
	/*
     * buildForm() method
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userEmail');
        $builder->add('userName');
        $builder->add('web');
        //Hidden field using the special entity_id type field
        $builder->add('post', 'entity_id', array(
            'class' => 'Hasheado\BlogBundle\Entity\BlogPost',
            'hidden' => true,
        ));
        $builder->add('content');
    }
}