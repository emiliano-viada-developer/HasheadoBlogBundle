<?php

namespace Hasheado\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog_comment")
 */
class BlogComment
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="BlogPost", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     * @Assert\NotBlank(message="This field is required.")
     */
    protected $post;

    /**
     * @ORM\Column(name="user_email", type="string", length=255)
     * @Assert\NotBlank(message="This field is required.")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    protected $userEmail;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="This field is required.")
     */
    protected $content;

    /**
     * @ORM\Column(name="is_accepted", type="boolean", options={"default"=FALSE})
     */
    protected $isAccepted;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /** magic methods **/
    public function __toString()
    {
        return $this->getContent();
    }
    /** End magic methods **/

    /** setters **/
    public function setId($id)
    {
    	$this->id = $id;
    }

    public function setPost($post)
    {
    	$this->post = $post;
    }

    public function setUserEmail($userEmail)
    {
    	$this->userEmail = $userEmail;
    }

    public function setContent($content)
    {
    	$this->content = $content;
    }

    public function setIsAccepted($isAccepted)
    {
    	$this->isAccepted = $isAccepted;
    }

    public function setCreatedAt($createdAt)
    {
    	$this->createdAt = $createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
    	$this->updatedAt = $updatedAt;
    }
    /** End setters **/

    /** getters **/
    public function getId()
    {
    	return $this->id;
    }

    public function getPost()
    {
    	return $this->post;
    }

    public function getUserEmail()
    {
    	return $this->userEmail;
    }

    public function getContent()
    {
    	return $this->content;
    }

    public function getIsAccepted()
    {
    	return $this->isAccepted;
    }

    public function getCreatedAt()
    {
    	return $this->createdAt;
    }

    public function getUpdatedAt()
    {
    	return $this->updatedAt;
    }
    /** End getters **/
}