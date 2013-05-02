<?php

namespace Hasheado\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class BlogPostRepository extends EntityRepository
{
	/**
	 * getLatest() method
	 * Gets the latest published posts
	 */
	public function getLatest()
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery(
    		'SELECT p, c
    		 FROM HasheadoBlogBundle:BlogPost p
    		 LEFT JOIN p.comments c
    		 WHERE p.isPublished = 1
    		 ORDER BY p.publishedAt DESC'
		);

		$posts = $query->getResult();

		return $posts;
	}
}