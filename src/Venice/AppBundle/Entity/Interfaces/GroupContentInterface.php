<?php
namespace Venice\AppBundle\Entity\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;
use Venice\AppBundle\Entity\Content\ContentInGroup;


/**
 * Class GroupContent.
 */
interface GroupContentInterface extends ContentInterface
{
    /**
     * @return ArrayCollection<ContentInGroup>
     */
    public function getItems();

    public function setItems($items);

    /**
     * @param ContentInGroup $item
     *
     * @return $this
     */
    public function addItem(ContentInGroup $item);

    /**
     * @param ContentInGroup $item
     *
     * @return $this
     */
    public function removeItem(ContentInGroup $item);

    /**
     * @return string
     */
    public function getHandle();

    /**
     * @param string $handle
     */
    public function setHandle($handle);

    /**
     * Create a new handle from given source and set it to the entity.
     *
     * @param $source String which will be source of a new handle.
     */
    public function createHandle($source);
}
