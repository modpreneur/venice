<?php

namespace Venice\AppBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Venice\AppBundle\Entity\Tag;

/**
 * Trait Taggable.
 */
trait Taggable
{
    /**
     * @var ArrayCollection<Tag>
     */
    protected $tags;

    /**
     * @return ArrayCollection<Tag>
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addTo($this);
        }

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->remove($tag);
        $tag->removeFrom($this);

        return $this;
    }
}
