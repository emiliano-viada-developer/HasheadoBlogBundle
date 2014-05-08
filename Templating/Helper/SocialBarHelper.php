<?php

namespace Hasheado\BlogBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;

class SocialBarHelper extends Helper
{
    protected $templating;

    /**
     * __constructor
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating  = $templating;
    }

    /**
     * socialButtons function
     * @param  $parameters
     * @return 
     */
    public function socialButtons($parameters)
    {
        return $this->templating->render('HasheadoBlogBundle:helper:socialButtons.html.twig', $parameters);
    }

    /**
     * facebookButton function
     * @param  $parameters
     * @return
     */
    public function facebookButton($parameters)
    {
        return $this->templating->render('HasheadoBlogBundle:helper:facebookButton.html.twig', $parameters);
    }

    /**
     * twitterButton function
     * @param  $parameters
     * @return
     */
    public function twitterButton($parameters)
    {
        return $this->templating->render('HasheadoBlogBundle:helper:twitterButton.html.twig', $parameters);
    }

    /**
     * googlePlusButton function
     * @param  $parameters
     * @return
     */
    public function googlePlusButton($parameters)
    {
        return $this->templating->render('HasheadoBlogBundle:helper:googlePlusButton.html.twig', $parameters);
    }

    /**
     * getName function
     */
    public function getName()
    {
        return 'socialButtons';
    }
}
