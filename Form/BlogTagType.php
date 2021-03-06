<?php

namespace Hasheado\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlogTagType extends AbstractType
{
    /*
     * buildForm() method
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
    }

    /*
     * getName() method
     */
    public function getName()
    {
        return 'tag';
    }

    /*
     * setDefaultOptions() method
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Hasheado\BlogBundle\Entity\BlogTag',
	    ));
	}
}