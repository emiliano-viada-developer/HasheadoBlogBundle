<?php

namespace Hasheado\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class BlogCommentRepository extends EntityRepository
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
			'content' => 'text',
			'post' => 'id',
			'userEmail' => 'text',
			'isAccepted' => 'boolean',
		);
	    $qb->select('c')
	        ->from('HasheadoBlogBundle:BlogComment', 'c');

		if (count($criteria)) {
			//Adding WHERE's
		    foreach ($criteria as $key => $value) {
		    	switch ($type[$key]) {
		    		case 'text':
		    			$qb->andWhere('c.' . $key . ' LIKE :' . $key);
		    			break;
	    			case 'id':
	    				if ($value != 'not_rel')
	    					$qb->andWhere('c.' . $key . ' = :' . $key);
	    				else
	    					$qb->andWhere('c.' . $key . ' IS NULL');
	    				break;
	    			case 'boolean':
	    			default:
	    				$qb->andWhere('c.' . $key . ' = :' . $key);
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
		    	$qb->addOrderBy('c.' . $field, $mode);
		    }
		}

		return $qb->getQuery();
	}

	/**
	 * getLatest method
	 * Returns latest comments
	 * @return ArrayCollection $comments
	 */
	public function getLatest($records = 5)
	{
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$comments = array();
		
	    $qb->select('c')
	        ->from('HasheadoBlogBundle:BlogComment', 'c')
	        ->andWhere('c.isAccepted = 1')
	        ->addOrderBy('c.createdAt', 'DESC');

	    $comments = $qb->getQuery()
	    			->setMaxResults($records)->getResult();

	    return $comments;
	}
}