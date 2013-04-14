<?php

namespace Hasheado\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="post")
 */
class Post
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

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
    /** end getters **/
}