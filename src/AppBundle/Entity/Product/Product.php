<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.10.15
 * Time: 13:28
 */

namespace AppBundle\Entity\Product;


use AppBundle\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Trinity\FrameworkBundle\Entity\BaseProduct as TrinityProduct;

/**
 * Class BaseProduct
 *
 * @ORM\Table(name="product")
 * @ORM\Entity()
 *
 * @package AppBundle\Entity\Product
 */
class Product extends TrinityProduct
{
    /**
     * @ORM\Column(name="handle", type="string", unique=true)
     * @var
     */
    protected $handle;


    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     *
     * @var
     */
    protected $image;


    /**
     * @var
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;


    /**
     * @var integer
     *
     * @ORM\Column(name="order_number", type="integer")
     */
    protected $orderNumber;


    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductAccess", mappedBy="product", cascade={"remove"})
     */
    protected $productAccesses;


    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ContentProduct", mappedBy="product", cascade={"remove"})
     */
    protected $contentProducts;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->enabled = true;
        $this->productAccesses = new ArrayCollection();
    }


    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }


    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

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
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
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


    public function setName($name)
    {
        $this->name = $name;
        $this->createHandle($name);
    }

    /**
     * @return ArrayCollection<Tag>
     */
    public function getTags()
    {
        return $this->tags;
    }


    /**
     * @param Tag $tag
     * @return $this
     */
    public function addTag(Tag $tag)
    {
        if(!$this->tags->contains($tag))
        {
            $this->tags->add($tag);
        }

        return $this;
    }


    /**
     * @param Tag $tag
     * @return $this
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->remove($tag);

        return $this;
    }


    /**
     * @return ArrayCollection<User>
     */
    public function getUsers()
    {
        return $this->users;
    }


    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user)
    {
        if(!$this->users->contains($user))
        {
            $this->users->add($user);
        }

        return $this;
    }


    /**
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user)
    {
        $this->users->remove($user);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }


    /**
     * @param mixed $enabled
     *
     * @return Product
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }


    /**
     * @param int $orderNumber
     *
     * @return Product
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

}