<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 16:03
 */

namespace AppBundle\Entity\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Content\ContentInGroup", mappedBy="group", cascade={"PERSIST"})
     * @ORM\OrderBy({"orderNumber" = "ASC"})
     */
    protected $items;


    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        /** @var ContentInGroup $item */
        foreach ($this->items as $item)
        {
            if($this->id == $item->getContent()->getId())
            {
                $context
                    ->buildViolation('Group content can not contain itself in "items" collection')
                    ->atPath('items')
                    ->addViolation();
            }
        }
    }


    public function __construct()
    {
        $this->items = new ArrayCollection();
    }


    /**
     * @return ArrayCollection<ContentInGroup>
     */
    public function getItems()
    {
        return $this->items;
    }


    public function setItems($items)
    {
        $this->items = $items;
    }


    /**
     * @param ContentInGroup $item
     * @return $this
     */
    public function addItem(ContentInGroup $item)
    {
        if(!$this->items->contains($item))
        {
            $this->items->add($item);
        }

        return $this;
    }


    /**
     * @param ContentInGroup $item
     * @return $this
     */
    public function removeItem(ContentInGroup $item)
    {
        if(!$this->items->contains($item))
        {
            $this->items->remove($item);
        }

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
        foreach ($this->items as $item)
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