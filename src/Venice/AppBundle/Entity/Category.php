<?php
/**
 * Created by PhpStorm.
 * User: marek
 * Date: 24/01/17
 * Time: 17:55
 */
namespace Venice\AppBundle\Entity;

use Trinity\Component\Core\Interfaces\EntityInterface;

/**
 * Class Category
 * @package Venice\AppBundle\Entity
 */
class Category implements EntityInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}