<?php

namespace Hasheado\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog_post")
 * @ORM\Entity(repositoryClass="Hasheado\BlogBundle\Entity\Repository\BlogPostRepository")
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
     * @ORM\ManyToOne(targetEntity="BlogCategory", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\Column(name="is_published", type="boolean", options={"default"=FALSE})
     */
    protected $isPublished;

    /**
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    protected $publishedAt;

    /**
     * @ORM\OneToMany(targetEntity="BlogComment", mappedBy="post")
     */
    protected $comments;

    /**
     * @ORM\ManyToMany(targetEntity="BlogTag", inversedBy="posts")
     * @ORM\JoinTable(name="blog_posts_tags",
     *      joinColumns={@ORM\JoinColumn(name="blog_post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="blog_tag_id", referencedColumnName="id")}
     *      )
     */
    protected $tags;

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
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /** magic methods **/
    public function __toString()
    {
        return $this->getTitle();
    }
    /** End magic methods **/

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

    public function setCategory($category)
    {
        $this->category = $category;
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

    public function setComments(ArrayCollection $comments)
    {
        $this->comments = $comments;
    }

    public function setTags($tags)
    {
        $this->tags = array($tags);
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

    public function getCategory()
    {
        return $this->category;
    }

    public function getIsPublished()
    {
        return $this->isPublished;
    }

    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getTags()
    {
        return $this->tags;
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

    /**
     * addTag() method
     * @param BlogTag $tag
     */
    public function addTag(BlogTag $tag)
    {
        $tag->addPost($this);
        $this->tags[] = $tag;
    }

    /**
     * removeTag() method
     * @param BlogTag $tag
     */
    public function removeTag(BlogTag $tag)
    {
        if (count($this->tags)) {
            foreach ($this->tags as $k => $existingTag) {
                if ($existingTag->getId() === $tag->getId())
                    unset($this->tags[$k]);
            }
        }
    }
}