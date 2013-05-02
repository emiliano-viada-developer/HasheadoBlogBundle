<?php

namespace Hasheado\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlogCommentType extends AbstractType
{
    /*
     * buildForm() method
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userEmail');
        $builder->add('userName');
        $builder->add('web');
        $builder->add('post');
        $builder->add('content');
        $builder->add('isAccepted', null, array('required' => false));
    }

    /*
     * getName() method
     */
    public function getName()
    {
        return 'comment';
    }

    /*
     * setDefaultOptions() method
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Hasheado\BlogBundle\Entity\BlogComment',
	    ));
	}
}