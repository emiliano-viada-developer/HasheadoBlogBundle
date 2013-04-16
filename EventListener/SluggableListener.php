<?php
namespace Hasheado\BlogBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Hasheado\BlogBundle\Entity\BlogPost as Post;
use Hasheado\BlogBundle\Util\Util;

class SluggableListener
{
	/**
	 * prePersist event to add sluggable behavior
	 */
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		//Sluggable Behavior
		if (property_exists(get_class($entity), 'slug')) {
			$entity->setSlug(Util::urlize($entity->getTitle(), "'"));
		}
	}
}