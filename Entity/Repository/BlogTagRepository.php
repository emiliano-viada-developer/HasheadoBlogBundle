<?php

namespace Hasheado\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class BlogTagRepository extends EntityRepository
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
			'name' => 'text',
		);
	    $qb->select('t')
	        ->from('HasheadoBlogBundle:BlogTag', 't');

		if (count($criteria)) {
			//Adding WHERE's
		    foreach ($criteria as $key => $value) {
		    	switch ($type[$key]) {
		    		case 'text':
		    			$qb->andWhere('t.' . $key . ' LIKE :' . $key);
		    			break;
	    			case 'id':
	    				if ($value != 'not_rel')
	    					$qb->andWhere('t.' . $key . ' = :' . $key);
	    				else
	    					$qb->andWhere('t.' . $key . ' IS NULL');
	    				break;
	    			case 'boolean':
	    			default:
	    				$qb->andWhere('t.' . $key . ' = :' . $key);
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
		    	$qb->addOrderBy('t.' . $field, $mode);
		    }
		}

		return $qb->getQuery();
	}

	/**
	 * getPopular method
	 * Returns popular tags based on posts
	 * @return ArrayCollection $tags
	 */
	public function getPopular($records = 5)
	{
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$months = array();
		
	    $qb->select('t', 'COUNT(p) AS num_posts')
	        ->from('HasheadoBlogBundle:BlogTag', 't')
	        ->leftJoin('t.posts', 'p')
	        ->where('p.isPublished = 1')
	        ->groupBy('t.id')
	        ->addOrderBy('num_posts', 'DESC')
	        ->addOrderBy('t.createdAt', 'DESC');

	    $tags = $qb->getQuery()
	    			->setMaxResults($records)->getResult();

	    return $tags;
	}
}
