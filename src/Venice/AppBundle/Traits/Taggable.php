<?php

namespace Venice\AppBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Venice\AppBundle\Entity\Interfaces\TagInterface;

/**
 * Trait Taggable.
 */
trait Taggable
{
    /**
     * @var ArrayCollection<TagInterface>
     */
    protected $tags;

    /**
     * @return ArrayCollection<TagInterface>
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param TagInterface $tag
     *
     * @return $this
     */
    public function addTag(TagInterface $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addTo($this);
        }

        return $this;
    }

    /**
     * @param TagInterface $tag
     *
     * @return $this
     */
    public function removeTag(TagInterface $tag)
    {
        $this->tags->remove($tag);
        $tag->removeFrom($this);

        return $this;
    }
}
