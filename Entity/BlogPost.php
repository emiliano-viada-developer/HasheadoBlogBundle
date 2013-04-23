<?php

namespace Hasheado\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog_post")
 * @UniqueEntity("slug")
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
     * @ORM\Column(type="boolean", options={"default"=FALSE})
     */
    protected $isPublished;

    /**
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    protected $publishedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true, options={"default"=NULL})
     */
    protected $slug;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

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

    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        //Set the publishedAt field
        if ($this->isPublished &&
            (is_null($this->publishedAt) || strtotime($this->publishedAt->format('Y-m-d')) < 0)) {
            $this->setPublishedAt(new \Datetime());
        }
    }

    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setupdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
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

    public function getIsPublished()
    {
        return $this->isPublished;
    }

    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /** end getters **/
}