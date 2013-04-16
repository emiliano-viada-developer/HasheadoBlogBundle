<?php

namespace Hasheado\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlogPostType extends AbstractType
{
    /*
     * buildForm() method
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('content');
    }

    /*
     * getName() method
     */
    public function getName()
    {
        return 'post';
    }

    /*
     * setDefaultOptions() method
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Hasheado\BlogBundle\Entity\BlogPost',
	    ));
	}
}