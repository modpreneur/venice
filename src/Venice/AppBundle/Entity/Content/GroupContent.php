<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 16:03.
 */
namespace Venice\AppBundle\Entity\Content;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GroupContent.
 */
class GroupContent extends Content
{
    const TYPE = 'group';

    /**
     * @var ArrayCollection<ContentInGroup>
     * @Serializer\Exclude()
     */
    protected $items;

    /**
     * @var string
     */
    protected $handle;

    /**
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        $items = array_values($this->items->toArray()); //reindex the array

        $count = $this->items->count(); //2
        //the for cycle is used for better indexing
        /* @noinspection ForeachInvariantsInspection */
        for ($i = 0; $i < $count; ++$i) {
            if ($items[$i]->getContent() && $this->id == $items[$i]->getContent()->getId()) {
                $context
                    ->buildViolation('Group content can not contain itself in "items" collection')
                    ->atPath('items')
                    ->addViolation();

                break;
            }
            for ($j = $i + 1; $j < $count-1; ++$j) { //i=0. j=1, i=1,j=2
                // If the item i in the collection twice
                if ($items[$i]->getGroup() == $items[$j]->getGroup()
                    && $items[$i]->getContent() == $items[$j]->getContent()
                    && $items[$i]->getDelay() === $items[$j]->getDelay()
                    && $items[$i]->getOrderNumber() === $items[$j]->getOrderNumber()
                ) {
                    $context
                        ->buildViolation(
                            'Group content can not contain the same content(with the same delay and order) twice.'
                        )
                        ->atPath('items')
                        ->addViolation();

                    break;
                }
            }
        }
    }

    /**
     * GroupContent constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->items = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<ContentInGroup>
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @param ContentInGroup $item
     *
     * @return $this
     */
    public function addItem(ContentInGroup $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }

        return $this;
    }

    /**
     * @param ContentInGroup $item
     *
     * @return $this
     */
    public function removeItem(ContentInGroup $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->remove($item);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        parent::setName($name);

        $this->createHandle($name);
    }

    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        if (empty($handle)) {
            $this->createHandle($this->name);
        } else {
            $this->handle = $handle;
        }
    }

    /**
     * Create a new handle from given source and set it to the entity.
     *
     * @param $source String which will be source of a new handle.
     */
    public function createHandle($source)
    {
        $this->handle = (new Slugify())->slugify($source);
    }

    /**
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
     */
    public function getContent()
    {
        $names = '';

        foreach ($this->items as $item) {
            $names .= $item->getContent()->getName().', ';
        }

        return $names;
    }

    /**
     * Get the content type string.
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
