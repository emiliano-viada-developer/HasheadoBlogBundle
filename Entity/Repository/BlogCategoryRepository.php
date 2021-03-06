<?php

namespace Hasheado\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class BlogCategoryRepository extends EntityRepository
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
	    $qb->select('c')
	        ->from('HasheadoBlogBundle:BlogCategory', 'c');

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
	 * getWithPosts method
	 * Returns categories with posts
	 * @return ArrayCollection $categories
	 */
	public function getWithPosts()
	{
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$categories = array();
		
	    $qb->select('p', 'c', 'COUNT(p) AS posts')
	        ->from('HasheadoBlogBundle:BlogPost', 'p')
	        ->leftJoin('p.category', 'c')
	        ->andWhere('p.isPublished = 1')
	        ->addGroupBy('p.category')
	        ->orderBy('posts', 'DESC');

	    $result = $qb->getQuery()->getResult();

	    if (count($result)) {
	    	foreach ($result as $k => $record) {
	    		$category = (is_null($record[0]->getCategory()))? 'Uncategorized' : $record[0]->getCategory();
	    		$categories[$k]['category'] = $category;
	    		$categories[$k]['posts'] = $record['posts'];
	    	}
	    }

	    return $categories;
	}
}