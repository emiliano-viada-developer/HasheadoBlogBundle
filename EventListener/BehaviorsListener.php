<?php
namespace Hasheado\BlogBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Hasheado\BlogBundle\Util\Util;

class BehaviorsListener
{
	/**
	 * onFLushevent to add behaviors
	 */
	public function onFlush(OnFlushEventArgs $eventArgs)
	{
		$em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        //For Insertions
        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
        	
        	$class = get_class($entity);
        	$meta = $em->getClassMetadata($class);

        	//Sluggable Behavior
			$entity = $this->sluggable($entity, $em);

			//Timestampable Behavior
			$entity = $this->timestampable($entity);
			
			$uow->recomputeSingleEntityChangeSet($meta, $entity);
        }

        //For updates
        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
        	
        	$class = get_class($entity);
        	$meta = $em->getClassMetadata($class);
        	
        	//Sluggable Behavior
			$entity = $this->sluggable($entity, $em);

			//Timestampable Behavior
			$entity = $this->timestampable($entity);
			
			$uow->recomputeSingleEntityChangeSet($meta, $entity);
        }
	}

	/**
	 * Sluggale behavior
	 */
	private function sluggable($entity, \Doctrine\ORM\EntityManager $em)
	{
		$class = get_class($entity);
		$qb = $em->createQueryBuilder();
		if (property_exists($class, 'slug')) {
			
			switch ($class) {
				case 'Hasheado\BlogBundle\Entity\BlogPost':
					$sluggable_field = $entity->getTitle();
					$repository = $em->getRepository('HasheadoBlogBundle:BlogPost');
					$en = 'HasheadoBlogBundle:BlogPost';
					break;
				case 'Hasheado\BlogBundle\Entity\BlogCategory':
					$sluggable_field = $entity->getName();
					$repository = $em->getRepository('HasheadoBlogBundle:BlogCategory');
					$en = 'HasheadoBlogBundle:BlogCategory';
					break;
				case 'Hasheado\BlogBundle\Entity\BlogTag':
					$sluggable_field = $entity->getName();
					$repository = $em->getRepository('HasheadoBlogBundle:BlogTag');
					$en = 'HasheadoBlogBundle:BlogTag';
					break;
				default:
					break;
			}

			$slug = Util::urlize($sluggable_field, "'");

			$qb->select('count(e.id)')
		        ->from($en, 'e')
		        ->where('e.slug = :p1')
		        ->andWhere('e.id != :p2')
		        ->setParameter('p1', $slug)
		        ->setParameter('p2', $entity->getId());
			
			$ocurrences = (integer) $qb->getQuery()->getSingleScalarResult();

			if ($ocurrences > 0) {
				$x = $ocurrences + 1;
				$slug .=  '-'.$x;
			}

			$entity->setSlug($slug);
		}

		return $entity;
	}

	/**
	 * Timestampable behavior
	 */
	private function timestampable($entity)
	{
		$class = get_class($entity);
		//created at
		if (property_exists($class, 'createdAt') && !$entity->getId()) {
			$entity->setCreatedAt(new \Datetime());
		}
		//updated at
		if (property_exists($class, 'updatedAt')) {
			$entity->setUpdatedAt(new \Datetime());
		}
		
		return $entity;
	}
}