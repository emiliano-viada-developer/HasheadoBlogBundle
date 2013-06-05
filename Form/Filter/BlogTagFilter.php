<?php

namespace Hasheado\BlogBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlogTagFilter extends AbstractType
{
    protected $defaults = array();
    //protected $em;

    /*public function __construct(array $defaults)
    {
        $this->defaults = $defaults;
    }*/

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'required' => false,
                'data' => (isset($this->defaults['name']))? $this->defaults['name'] : null,
            ));
    }

    public function getName()
    {
        return 'tag_filter';
    }
}
