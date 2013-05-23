<?php

namespace Hasheado\BlogBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class BlogCommentFilter extends AbstractType
{
    protected $defaults = array();
    protected $em;

    public function __construct(array $defaults, EntityManager $em)
    {
        $this->defaults = $defaults;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Posts
        $posts = $this->em->getRepository('HasheadoBlogBundle:BlogPost')->findAll();
        $post_choices = array();
        if (count($posts)) {
            foreach ($posts as $post) {
                $post_choices[$post->getId()] = $post->getTitle();
            }
        }

        $builder
            ->add('content', null, array(
                'required' => false,
                'data' => (isset($this->defaults['content']))? $this->defaults['content'] : null,
            ))
            ->add('post', 'choice', array(
                'required' => false,
                'choices' => $post_choices,
                'empty_value' => 'Choose a post',
                'data' => (isset($this->defaults['post']))? $this->defaults['post'] : null,
            ))
            ->add('userEmail', null, array(
                'required' => false,
                'data' => (isset($this->defaults['userEmail']))? $this->defaults['userEmail'] : null,
            ))
            ->add('isAccepted', 'choice', array(
                'required' => false,
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'empty_value' => '-----',
                'data' => (isset($this->defaults['isAccepted']))? $this->defaults['isAccepted'] : null,
            ));
    }

    public function getName()
    {
        return 'comment_filter';
    }
}
