<?php
namespace Hasheado\BlogBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Hasheado\BlogBundle\Entity\BlogTag;

class TagToTextTransformer implements DataTransformerInterface
{
	/**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms a collection of objects (tags) to an array.     *
     * @param  PersistentCollection $collection
     * @return string
     */
    public function transform($collection)
    {
        if ($collection === null)
        	return array();

        $tagsArray = array();
        foreach ($collection as $tag) {
            $tagsArray[] = $tag->getId();
        }
        
		return $tagsArray;
    }

    /**
     * Transforms an array (some tag ids) to a collection of objects (tags).     *
     * @param  string $tagsArray
     * @return
     */
    public function reverseTransform($tagsArray)
    {
        $collection = new ArrayCollection();
        if (!is_array($tagsArray) && count($tagsArray) == 0) {
            return null;
        }

        foreach ($tagsArray as $tagValue) {
            $tag = $this->om
                ->getRepository('HasheadoBlogBundle:BlogTag')
                ->findOneById($tagValue);

            if ($tag) {
                $collection[] = $tag;
            } else {
                //Add the new Tag
                $tag = new BlogTag();
                $tag->setName(ucfirst($tagValue));
                $this->om->persist($tag);
                $this->om->flush();
                $collection[] = $tag;
            }
        }

        return $collection;
    }
}