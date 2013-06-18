<?php

namespace Hasheado\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog_tag")
 * @ORM\Entity(repositoryClass="Hasheado\BlogBundle\Entity\Repository\BlogTagRepository")
 * @UniqueEntity("slug")
 */
class BlogTag
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
     * @ORM\ManyToMany(targetEntity="BlogPost", mappedBy="tags")
     */
    protected $posts;

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

    /** Contructor **/
    public function __contruct()
    {
        $this->posts = new ArrayCollection();
    }

    /** magic methods **/
    public function __toString()
    {
        return $this->getName();
    }
    /** End magic methods **/    

    /** setters **/
    public function setId($id)
    {
    	$this->id = $id;
    }

    public function setName($name)
    {
    	$this->name = $name;
    }

    public function setPosts(ArrayCollection $posts)
    {
        $this->posts = $posts;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt($updatedAt)
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

    public function getPosts()
    {
        return $this->posts;
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

    public function addPost(BlogPost $post)
    {
        $this->posts[] = $post;
    }
}