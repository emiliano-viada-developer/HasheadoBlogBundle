<?php

namespace Hasheado\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Hasheado\BlogBundle\Form\DataTransformer\TagToTextTransformer;
use Hasheado\BlogBundle\Util\Util;

class BlogPostType extends AbstractType
{
    /*
     * buildForm() method
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $extraChoices = (isset($options['extra_choices']))? $options['extra_choices'] : array();
        $entityManager = $options['em'];
        $transformer = new TagToTextTransformer($entityManager);

        $builder->add('title');
        $builder->add('content', 'wysiwyg', array(
            'required' => true,
            'attr' => array('rows' => 25),
        ));
        $builder->add('category', null, array(
            'empty_value' => 'Choose an option',
        ));

        $tagChoices = array();
        $tags = $entityManager->getRepository('HasheadoBlogBundle:BlogTag')->findAll();
        if (count($tags)) {
            foreach ($tags as $tag) {
                $tagChoices[$tag->getId()] = $tag->getName();
            }
        }
        if (count($extraChoices)) {
            foreach ($extraChoices as $key => $value) {
                if (!in_array($value, $tagChoices))
                    $tagChoices[$key] = $value;
            }
        }
        $builder->add(
            $builder->create('tags', 'choice', array(
                'choices' => $tagChoices,
                'expanded' => false,
                'required' => false,
                'multiple' => true,
                'attr' => array(
                    'class' => 'chzn-select',
                    'multiple' => null,
                    'data-placeholder' => 'Enter some tags',
                ),
            ))
            ->addModelTransformer($transformer)
        );
        $builder->add('isPublished', null, array('required' => false));
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

        $resolver->setRequired(array(
            'em',
        ));

        $resolver->setOptional(array(
            'extra_choices',
        ));

        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
	}

    public function validateMethod(Event $event, ExecutionContextInterface $context)
    {
        $context->addViolationAt('tags', 'There is already an event during this time!');
    }
}