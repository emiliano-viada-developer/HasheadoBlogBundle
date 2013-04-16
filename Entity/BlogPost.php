<?php

namespace Hasheado\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog_post")
 */
class BlogPost
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="This field is required.")
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="This field is required.")
     */
    protected $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default"=NULL})
     */
    protected $slug;

    /** setters **/
    public function setId($id)
    {
    	$this->id = $id;
    }

    public function setTitle($title)
    {
    	$this->title = $title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
    /** end setters **/

    
    /** getters **/
    public function getId()
    {
    	return $this->id;
    }

    public function getTitle()
    {
    	return $this->title;
    }

    public function getContent()
    {
    	return $this->content;
    }

    public function getSlug()
    {
        return $this->slug;
    }
    /** end getters **/
}