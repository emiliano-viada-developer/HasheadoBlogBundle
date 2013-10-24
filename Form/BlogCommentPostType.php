<?php

namespace Hasheado\BlogBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
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
        //Hidden field to antispam
        $builder->add('antispam', 'hidden', array(
            'mapped' => false
        ));
        //Validate antispam field
        $builder->addEventListener(FormEvents::POST_BIND, function(FormEvent $event) {
            $form = $event->getForm();
            $antispamValue = $form['antispam']->getData();
            if (!empty($antispamValue)) {
                $form->addError(new FormError('SPAM: You are a BOT!.'));
            }
        });
        $builder->add('content');
    }
}