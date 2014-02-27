<?php

/**
 * This is a class from Gregwar FormBundle, please refer to original source:
 * https://github.com/Gregwar/FormBundle
 */
namespace Hasheado\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Kernel;

use Hasheado\BlogBundle\Form\DataTransformer\EntityToIdTransformer;

/**
 * Entity identitifer
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class EntityIdType extends AbstractType
{
    protected $registry;

    /**
     * Constructor
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * buildForm() function
     * @param  FormBuilderInterface $builder
     * @param  array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->registry->getManager($options['em']);
        
        $builder->addModelTransformer(new EntityToIdTransformer(
            $em,
            $options['class'],
            $options['property'],
            $options['query_builder'],
            $options['multiple']
        ));
    }

    /**
     * setDefaultOptions() function
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'class',
        ));

        $resolver->setDefaults(array(
            'em'            => null,
            'property'      => null,
            'query_builder' => null,
            'hidden'        => true,
            'multiple'      => false,
        ));
    }

    /**
     * buildView() function
     * @param  FormView $view
     * @param  FormInterface $form
     * @param  array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (true === $options['hidden']) {
            $view->vars['type'] = 'hidden';
        }
    }

    /**
     * getParent() function
     * @return string $parentName
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * getName() function
     * @return string $name
     */
    public function getName()
    {
        return 'entity_id';
    }
}