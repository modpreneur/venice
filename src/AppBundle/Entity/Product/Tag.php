<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.10.15
 * Time: 14:34
 */

namespace AppBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tag
 *
 * @ORM\Entity()
 *
 * @package AppBundle\Entity\Product
 */
class Tag
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    protected $name;


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
}