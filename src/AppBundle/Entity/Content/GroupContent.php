<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 16:03
 */

namespace AppBundle\Entity\Content;

use AppBundle\Entity\ContentInGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="content_group")
 *
 * Class GroupContent
 * @package AppBundle\Entity\Content
 */
class GroupContent extends Content
{
    /**
     * @var ArrayCollection<ContentInGroup>
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ContentInGroup", mappedBy="group")
     */
    protected $contentsInGroup;


    public function __construct()
    {
        $this->contentsInGroup = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<ContentInGroup>
     */
    public function getContentsInGroup()
    {
        return $this->contentsInGroup;
    }


    /**
     * @param ContentInGroup $contentInGroup
     * @return $this
     */
    public function addContentInGroup(ContentInGroup $contentInGroup)
    {
        if(!$this->contentsInGroup->contains($contentInGroup))
        {
            $this->contentsInGroup->add($contentInGroup);
        }

        return $this;
    }


    /**
     * @param ContentInGroup $contentInGroup
     * @return $this
     */
    public function removeContentInGroup(ContentInGroup $contentInGroup)
    {
        $this->contentsInGroup->remove($contentInGroup);

        return $this;
    }


    /**
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
     *
     */
    public function getContent()
    {
        $names = "";
        foreach ($this->contentsInGroup as $item)
        {
            $names .= $item->getContent()->getName() . ", ";
        }

        return $names;
    }


    /**
     * Get the content type string
     *
     * @return string
     */
    public function getType()
    {
        return "group";
    }
}