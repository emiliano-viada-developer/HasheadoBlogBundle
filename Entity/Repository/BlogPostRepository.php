<?php

namespace Hasheado\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class BlogPostRepository extends EntityRepository
{
	/**
	 * getListQuery() method
	 * Return a Doctrine_Query object based on some params
	 */
	public function getListQuery(array $criteria, array $orderBy = null)
	{
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		//Define the type of searching
		$type = array(
			'title' => 'text',
			'category' => 'id',
			'isPublished' => 'boolean',
		);
	    $qb->select('p')
	        ->from('HasheadoBlogBundle:BlogPost', 'p');

		if (count($criteria)) {
			//Adding WHERE's
		    foreach ($criteria as $key => $value) {
		    	switch ($type[$key]) {
		    		case 'text':
		    			$qb->andWhere('p.' . $key . ' LIKE :' . $key);
		    			break;
	    			case 'id':
	    				if ($value != 'not_rel')
	    					$qb->andWhere('p.' . $key . ' = :' . $key);
	    				else
	    					$qb->andWhere('p.' . $key . ' IS NULL');
	    				break;
	    			case 'boolean':
	    			default:
	    				$qb->andWhere('p.' . $key . ' = :' . $key);
	    				break;
		    	}
		    }
		    //Setting parameters
		    foreach ($criteria as $key => $value) {
		    	switch ($type[$key]) {
		    		case 'text':
		    			$qb->setParameter($key, '%' . $value . '%');
		    			break;
		    		case 'id':
		    			if ($value != 'not_rel')
		    				$qb->setParameter($key, $value);
		    			break;
		    		case 'boolean':
		    		default:
		    			$qb->setParameter($key, $value);
		    			break;
		    	}
		    }
		}

		//orderBy
		if (count($orderBy)) {
			foreach ($orderBy as $field => $mode) {
		    	$qb->addOrderBy('p.' . $field, $mode);
		    }
		}

		return $qb->getQuery();
	}

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