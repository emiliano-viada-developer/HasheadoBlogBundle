<?php

namespace Hasheado\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog_category")
 * @UniqueEntity("slug")
 */
class BlogCategory
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
    protected $name;

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

    public function setName($name)
    {
    	$this->name = $name;
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

    public function getName()
    {
    	return $this->name;
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