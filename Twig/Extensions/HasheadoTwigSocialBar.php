<?php

namespace Hasheado\BlogBundle\Twig\Extensions;

class HasheadoTwigSocialBar extends \Twig_Extension
{
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    /**
     * getName() function
     */
    public function getName()
    {
        return 'hasheado_social_bar';
    }
    
    /**
     * getFunctions() function
     * @return Array
     */
    public function getFunctions()
    {
        return array(
            'socialButtons' => new \Twig_Function_Method($this, 'getSocialButtons', array('is_safe' => array('html'))),
            'facebookButton' => new \Twig_Function_Method($this, 'getFacebookLikeButton', array('is_safe' => array('html'))),
            'twitterButton' => new \Twig_Function_Method($this, 'getTwitterButton', array('is_safe' => array('html'))),
            'googlePlusButton' => new \Twig_Function_Method($this, 'getGooglePlusButton', array('is_safe' => array('html'))),
        );
    }

    /**
     * getSocialButtons() function
     * @param Array $parameters
     */
    public function getSocialButtons($parameters = array())
    {
        //FACEBOOK
        // no parameters were defined, keeps default values
        if (!array_key_exists('facebook', $parameters)) {
            $render_parameters['facebook'] = array();
        // parameters are defined, overrides default values
        } else if(is_array($parameters['facebook'])) {
            $render_parameters['facebook'] = $parameters['facebook'];
        // the button is not displayed 
        } else {
          $render_parameters['facebook'] = false;
        }

        //TWITTER
        if (!array_key_exists('twitter', $parameters)) {
            $render_parameters['twitter'] = array();
        } else if(is_array($parameters['twitter'])) {
            $render_parameters['twitter'] = $parameters['twitter'];
        } else {
            $render_parameters['twitter'] = false;
        }

        //GOOGLE PLUS
        if (!array_key_exists('googleplus', $parameters)) {
            $render_parameters['googleplus'] = array();
        } else if(is_array($parameters['googleplus'])) {
            $render_parameters['googleplus'] = $parameters['googleplus'];
        } else {
            $render_parameters['googleplus'] = false;
        }

        // get the helper service and display the template
        return $this->container->get('hasheado_blog.socialBarHelper')->socialButtons($render_parameters);
    }
 
    // https://developers.facebook.com/docs/reference/plugins/like/ 
    public function getFacebookLikeButton($parameters = array())
    {
       // default values, you can override the values by setting them
       $parameters = $parameters + array(
            'url' => null,
            'locale' => 'es_ES',
            'send' => false,
            'width' => 300,
            'showFaces' => false,
            'layout' => 'button_count',
        );

       return $this->container->get('hasheado_blog.socialBarHelper')->facebookButton($parameters);
    }

    /**
     * getTwitterButton() function
     * @param Array $parameters
     */
    public function getTwitterButton($parameters = array())
    {
       $parameters = $parameters + array(
            'url' => null,
            'locale' => 'es',
            'message' => 'Interesante post',
            'text' => 'Tweet',
            'via' => 'hasheado.com',
            'tag' => '#hasheado',           
        );

        return $this->container->get('hasheado_blog.socialBarHelper')->twitterButton($parameters);
    }

    /**
     * getGooglePlusButton() function
     * @param Array $parameters
     */
    public function getGooglePlusButton($parameters = array())
    {
       $parameters = $parameters + array(
            'url' => null,
            'locale' => 'es',
            'size' => 'medium',
            'annotation' => 'bubble',
            'width' => '300',
        );

        return $this->container->get('hasheado_blog.socialBarHelper')->googlePlusButton($parameters);
    }
}
