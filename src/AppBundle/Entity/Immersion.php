<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:54
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class Immersion
 *
 * @ORM\Entity()
 * @ORM\Table(name="immersion")
 *
 * @package AppBundle\Entity
 */
class Immersion
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
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinTable(name="users_immersions")
     */
    protected $user;


    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\Product")
     * @ORM\JoinTable(name="users_products")
     */
    protected $product;


    /**
     * @var
     *
     * @ORM\Column(name="start", type="datetime")
     */
    protected $start;


    /**
     * @var
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    protected $end;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }


    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }


    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }


    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }


    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }


    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }


}