<?php

namespace Hasheado\BlogBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class BlogPostFilter extends AbstractType
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
        //Categories
        $categories = $this->em->getRepository('HasheadoBlogBundle:BlogCategory')->findAll();
        $category_choices = array('not_rel' => 'Uncategorized');
        if (count($categories)) {
            foreach ($categories as $category) {
                $category_choices[$category->getId()] = $category->getName();
            }
        }

        $builder
            ->add('title', null, array(
                'required' => false,
                'data' => (isset($this->defaults['title']))? $this->defaults['title'] : null,
            ))
            ->add('category', 'choice', array(
                'required' => false,
                'choices' => $category_choices,
                'empty_value' => 'Choose a category',
                'data' => (isset($this->defaults['category']))? $this->defaults['category'] : null,
            ))
            ->add('isPublished', 'choice', array(
                'required' => false,
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'empty_value' => '-----',
                'data' => (isset($this->defaults['isPublished']))? $this->defaults['isPublished'] : null,
            ));
    }

    public function getName()
    {
        return 'post_filter';
    }
}
