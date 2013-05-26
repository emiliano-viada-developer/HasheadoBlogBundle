<?php

namespace Hasheado\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WysiwygType extends AbstractType
{
    /**
     * buildView() function
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //Add the class wysiwyg to apply rich editor
        $attr = $view->vars['attr'];
        $add_class = (isset($attr['class']))? $attr['class'] . ' wysiwyg' : 'wysiwyg';
        $attr['class'] = $add_class;
        $view->vars['attr'] = $attr;

        $options['options'] = array_merge(array('selector' => 'textarea.wysiwyg'), $options['options']);
        if (!isset($options['options']['plugins'])) {
            $options['options']['plugins'] = 'link image code';
        }
        if (!isset($options['options']['menu'])) {
            $options['options']['menu'] = 'undo redo visualaid cut copy paste selectall bold italic underline strikethrough 
                    subscript superscript removeformat formats link image code';
        }
        $options['options']['image_advtab'] = true;
        $settings = null;
        if (count($options['options'])) {
            $settings = json_encode($options['options']);
        }
        $view->vars['_settings'] = $settings;
    }

    /**
     * setDefaultOptions() function
     * //See http://redactorjs.com/docs/settings
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array(),
        ));
    }

    /**
     * getParent() function
     * @return string $parentName
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * getName() function
     * @return string $name
     */
    public function getName()
    {
        return 'wysiwyg';
    }
}